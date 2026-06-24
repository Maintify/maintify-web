-- ============================================================
-- MAINTIFY - Supabase Schema
-- Jalankan script ini di Supabase SQL Editor
-- Dashboard → SQL Editor → New Query → Paste → Run
-- ============================================================


-- ------------------------------------------------------------
-- 1. USERS TABLE (sudah dibuat Laravel, tambahkan kolom role)
-- ------------------------------------------------------------
-- Jika users table belum ada, jalankan migration Laravel dulu:
--   php artisan migrate
--
-- Tabel ini dikelola oleh Laravel Migration.
-- Script ini hanya untuk referensi dan RLS Policy.


-- ------------------------------------------------------------
-- 2. WORKSHOPS TABLE
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS workshops (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name            VARCHAR(255) NOT NULL,
    phone           VARCHAR(50),
    email           VARCHAR(255),
    address         TEXT,
    city            VARCHAR(100),
    province        VARCHAR(100),
    postal_code     VARCHAR(10),
    description     TEXT,
    logo_url        VARCHAR(500),
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),
    deleted_at      TIMESTAMP NULL
);

CREATE INDEX IF NOT EXISTS idx_workshops_user_id ON workshops(user_id);
CREATE INDEX IF NOT EXISTS idx_workshops_is_active ON workshops(is_active);


-- ------------------------------------------------------------
-- 3. VEHICLES TABLE
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS vehicles (
    id                      BIGSERIAL PRIMARY KEY,
    user_id                 BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    plate_number            VARCHAR(20) UNIQUE NOT NULL,
    brand                   VARCHAR(100) NOT NULL,
    model                   VARCHAR(100) NOT NULL,
    type                    VARCHAR(100),
    year                    SMALLINT NOT NULL,
    color                   VARCHAR(50),
    engine_number           VARCHAR(100),
    chassis_number          VARCHAR(100),
    current_odometer        INTEGER DEFAULT 0,
    next_service_odometer   INTEGER,
    next_service_date       DATE,
    qr_code                 VARCHAR(20) UNIQUE,
    qr_code_url             VARCHAR(500),
    photo_url               VARCHAR(500),
    health_status           VARCHAR(20) DEFAULT 'good' CHECK (health_status IN ('good', 'warning', 'critical')),
    health_score            SMALLINT DEFAULT 100 CHECK (health_score >= 0 AND health_score <= 100),
    is_active               BOOLEAN DEFAULT TRUE,
    created_at              TIMESTAMP DEFAULT NOW(),
    updated_at              TIMESTAMP DEFAULT NOW(),
    deleted_at              TIMESTAMP NULL
);

CREATE INDEX IF NOT EXISTS idx_vehicles_user_id ON vehicles(user_id);
CREATE INDEX IF NOT EXISTS idx_vehicles_plate_number ON vehicles(plate_number);
CREATE INDEX IF NOT EXISTS idx_vehicles_qr_code ON vehicles(qr_code);
CREATE INDEX IF NOT EXISTS idx_vehicles_health_status ON vehicles(health_status);


-- ------------------------------------------------------------
-- 4. SERVICE_HISTORIES TABLE
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS service_histories (
    id                      BIGSERIAL PRIMARY KEY,
    vehicle_id              BIGINT NOT NULL REFERENCES vehicles(id) ON DELETE CASCADE,
    workshop_id             BIGINT NOT NULL REFERENCES workshops(id) ON DELETE CASCADE,
    technician_id           BIGINT REFERENCES users(id) ON DELETE SET NULL,
    service_type            VARCHAR(50) NOT NULL DEFAULT 'periodic_service'
                                CHECK (service_type IN (
                                    'oil_change', 'tune_up', 'periodic_service',
                                    'repair', 'tire_change', 'brake_service', 'other'
                                )),
    service_type_label      VARCHAR(255),
    service_date            DATE NOT NULL,
    odometer_in             INTEGER NOT NULL,
    odometer_out            INTEGER,
    next_service_odometer   INTEGER,
    next_service_date       DATE,
    cost                    NUMERIC(12, 2) DEFAULT 0,
    notes                   TEXT,
    parts_replaced          TEXT,
    invoice_number          VARCHAR(100),
    created_at              TIMESTAMP DEFAULT NOW(),
    updated_at              TIMESTAMP DEFAULT NOW(),
    deleted_at              TIMESTAMP NULL
);

CREATE INDEX IF NOT EXISTS idx_service_vehicle_date ON service_histories(vehicle_id, service_date);
CREATE INDEX IF NOT EXISTS idx_service_workshop_date ON service_histories(workshop_id, service_date);
CREATE INDEX IF NOT EXISTS idx_service_type ON service_histories(service_type);


-- ------------------------------------------------------------
-- 5. ACTIVITY_LOGS TABLE
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_logs (
    id          BIGSERIAL PRIMARY KEY,
    user_id     BIGINT REFERENCES users(id) ON DELETE SET NULL,
    action      VARCHAR(100) NOT NULL,
    model_type  VARCHAR(100),
    model_id    BIGINT,
    description TEXT,
    old_values  JSONB,
    new_values  JSONB,
    ip_address  VARCHAR(45),
    user_agent  TEXT,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_logs_user_date ON activity_logs(user_id, created_at);
CREATE INDEX IF NOT EXISTS idx_logs_model ON activity_logs(model_type, model_id);


-- ============================================================
-- ROW LEVEL SECURITY (RLS) POLICIES
-- Aktifkan setelah tabel dibuat dan data awal dimasukkan
-- ============================================================

-- Enable RLS
ALTER TABLE workshops       ENABLE ROW LEVEL SECURITY;
ALTER TABLE vehicles        ENABLE ROW LEVEL SECURITY;
ALTER TABLE service_histories ENABLE ROW LEVEL SECURITY;
ALTER TABLE activity_logs   ENABLE ROW LEVEL SECURITY;

-- ------------------------------------------------------------
-- WORKSHOPS Policies
-- ------------------------------------------------------------

-- Super admin bisa akses semua
CREATE POLICY "super_admin_all_workshops"
    ON workshops FOR ALL
    USING (
        EXISTS (
            SELECT 1 FROM users WHERE users.id = auth.uid()::BIGINT AND users.role = 'super_admin'
        )
    );

-- Bengkel bisa akses data bengkelnya sendiri
CREATE POLICY "workshop_own_data"
    ON workshops FOR ALL
    USING (user_id = auth.uid()::BIGINT);

-- Semua authenticated user bisa READ bengkel aktif
CREATE POLICY "read_active_workshops"
    ON workshops FOR SELECT
    USING (is_active = TRUE AND deleted_at IS NULL);


-- ------------------------------------------------------------
-- VEHICLES Policies
-- ------------------------------------------------------------

-- Super admin bisa akses semua
CREATE POLICY "super_admin_all_vehicles"
    ON vehicles FOR ALL
    USING (
        EXISTS (
            SELECT 1 FROM users WHERE users.id = auth.uid()::BIGINT AND users.role = 'super_admin'
        )
    );

-- Pemilik bisa akses kendaraan sendiri
CREATE POLICY "owner_own_vehicles"
    ON vehicles FOR ALL
    USING (user_id = auth.uid()::BIGINT);

-- Bengkel bisa READ kendaraan yang pernah diservice (untuk scan QR)
CREATE POLICY "workshop_read_vehicles"
    ON vehicles FOR SELECT
    USING (
        EXISTS (
            SELECT 1 FROM users WHERE users.id = auth.uid()::BIGINT AND users.role = 'workshop'
        )
    );


-- ------------------------------------------------------------
-- SERVICE_HISTORIES Policies
-- ------------------------------------------------------------

-- Super admin bisa akses semua
CREATE POLICY "super_admin_all_service"
    ON service_histories FOR ALL
    USING (
        EXISTS (
            SELECT 1 FROM users WHERE users.id = auth.uid()::BIGINT AND users.role = 'super_admin'
        )
    );

-- Bengkel bisa CRUD service yang dikerjakan bengkel ini
CREATE POLICY "workshop_own_service"
    ON service_histories FOR ALL
    USING (
        workshop_id IN (
            SELECT id FROM workshops WHERE user_id = auth.uid()::BIGINT
        )
    );

-- Pemilik kendaraan bisa READ histori kendaraannya
CREATE POLICY "owner_read_service"
    ON service_histories FOR SELECT
    USING (
        vehicle_id IN (
            SELECT id FROM vehicles WHERE user_id = auth.uid()::BIGINT
        )
    );


-- ------------------------------------------------------------
-- ACTIVITY_LOGS Policies
-- ------------------------------------------------------------

-- Super admin bisa akses semua log
CREATE POLICY "super_admin_all_logs"
    ON activity_logs FOR ALL
    USING (
        EXISTS (
            SELECT 1 FROM users WHERE users.id = auth.uid()::BIGINT AND users.role = 'super_admin'
        )
    );

-- User hanya bisa READ log aktivitasnya sendiri
CREATE POLICY "user_own_logs"
    ON activity_logs FOR SELECT
    USING (user_id = auth.uid()::BIGINT);


-- ============================================================
-- STORAGE BUCKETS
-- Buat bucket ini di Supabase Dashboard → Storage
-- ============================================================
-- Bucket: vehicle-photos   (Public)  → Foto kendaraan
-- Bucket: qr-codes         (Public)  → Gambar QR Code
-- Bucket: workshop-logos   (Public)  → Logo bengkel

-- ============================================================
-- DONE
-- ============================================================
