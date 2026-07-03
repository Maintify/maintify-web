# Phase 3 - Authentication Module ✅

## Completed Tasks

### User Authentication ✅
- ✅ User Login — Form sudah ada dengan dark theme, password visibility toggle
- ✅ User Register — Form dengan pilihan role (Pemilik Kendaraan / Bengkel Mitra)
- ✅ Forgot Password — Form kirim reset link sudah ada
- ✅ Logout — Logout button di sidebar & topbar dropdown
- ✅ Session Management — Laravel session dengan Supabase PostgreSQL

### Workshop Authentication ✅
- ✅ Workshop Registration — Form pendaftaran bengkel lengkap dengan:
  - Informasi akun (nama, email, password)
  - Informasi bengkel (nama bengkel, telepon, alamat, kota, provinsi)
  - Status approval `pending` setelah register
- ✅ Workshop Approval Flow — Middleware & halaman pending untuk bengkel yang menunggu approval
- ✅ Workshop Login — Menggunakan form login yang sama, auto-redirect ke pending jika belum approved

### Super Admin Authentication ✅
- ✅ Super Admin Login — Menggunakan form login yang sama
- ✅ Super Admin Seeder — AdminSeeder untuk buat akun super admin pertama
- ✅ Session Management — Menggunakan session yang sama dengan user lain

---

## File Changes

### New Controllers
- `app/Http/Controllers/Auth/WorkshopRegistrationController.php` — Handle pendaftaran bengkel

### New Middleware
- `app/Http/Middleware/WorkshopApprovedMiddleware.php` — Cek status approval bengkel

### New Views
- `resources/views/auth/register-workshop.blade.php` — Form pendaftaran bengkel
- `resources/views/workshop/pending.blade.php` — Halaman pending approval untuk bengkel

### Updated Views
- `resources/views/auth/register.blade.php` — Tambah pilihan role di form register
- `resources/views/auth/forgot-password.blade.php` — Update ke design system baru (dark theme)

### New Migration
- `database/migrations/2026_06_30_000001_add_status_to_workshops_table.php` — Tambah kolom:
  - `status` (pending/approved/rejected)
  - `rejection_reason`
  - `approved_at`
  - `approved_by`

### Updated Models
- `app/Models/Workshop.php` — Tambah:
  - Constants: `STATUS_PENDING`, `STATUS_APPROVED`, `STATUS_REJECTED`
  - Relation: `approver()` → User yang approve
  - Fillable: status, rejection_reason, approved_at, approved_by

- `app/Models/User.php` — Relasi sudah ada dari Phase 1

### Updated Routes
- `routes/web.php` — Tambah route:
  - `GET /register/workshop` — Form register bengkel
  - `POST /register/workshop` — Submit register bengkel
  - `GET /workshop/pending` — Halaman pending approval

### Updated Config
- `bootstrap/app.php` — Register middleware alias `workshop.approved`

---

## Database Changes

Jalankan migration baru:
```bash
php artisan migrate
```

Atau langsung jalankan SQL ini di Supabase SQL Editor:
```sql
ALTER TABLE workshops 
ADD COLUMN status VARCHAR(20) DEFAULT 'pending',
ADD COLUMN rejection_reason TEXT,
ADD COLUMN approved_at TIMESTAMP,
ADD COLUMN approved_by BIGINT REFERENCES users(id) ON DELETE SET NULL;
```

---

## Credentials untuk Testing

### Super Admin (via seeder)
```
Email: admin@maintify.app
Password: password
```

Buat akun ini dengan:
```bash
php artisan db:seed --class=AdminSeeder
```

### User Biasa
Register via form di `/register`, pilih role **Pemilik Kendaraan**

### Bengkel
Register via form di `/register/workshop`, akan redirect ke halaman pending setelah submit

---

## Workshop Approval Flow

1. **Register** — Bengkel daftar via form `/register/workshop`
2. **Pending** — Status workshop = `pending`, redirect ke `/workshop/pending`
3. **Admin Approval** — (Nanti di Phase 14) Admin approve/reject via panel admin
4. **Approved** — Status = `approved`, `is_active` = true, bengkel bisa akses dashboard penuh
5. **Rejected** — Status = `rejected`, tampil alasan penolakan

---

## Next Steps (Phase 4)

Phase 4 sudah selesai juga di development, tapi masih pakai data dummy. Yang perlu dilakukan:
1. Integrasi data real dari database ke dashboard
2. KPI cards dengan query ke tabel vehicles, service_histories, workshops
3. Chart dengan data real

---

## Notes

- Workshop approval saat ini manual via SQL atau tinker
- Nanti di Phase 14 akan ada UI admin panel untuk approve/reject bengkel
- Middleware `workshop.approved` otomatis block bengkel pending dari akses dashboard
- Session menggunakan database driver (tabel `sessions`) via Supabase PostgreSQL
