# TASK LIST — MAINTIFY

### Structured Implementation Tasks (Epic → Feature → Task → Subtask)

**Version:** 2.0 | **Date:** 02 July 2026
**References:** PRD.md, erd.md, plan.md
**Tech Stack (Actual):** Laravel (PHP), Blade, TailwindCSS, Supabase
**Status Legend:** `[ ]` To Do · `[/]` In Progress · `[x]` Done · `[~]` Skipped / Deferred

---

## EPIC 1: FOUNDATION & INFRASTRUCTURE

> Establish the project base, database schema, CI/CD, and hosting so all subsequent features have a stable platform.

---

### Feature 1.1: Project Setup & Configuration

#### Task 1.1.1: Initialize Laravel Project & Development Environment

- **Objective:** Bootstrap the Laravel application with all required dependencies, configure environment variables, and ensure the dev server runs cleanly.
- **Dependencies:** None (root task)
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - `php artisan serve` starts without errors.
  - `.env` is configured for local development (database, mail, app key).
  - `composer install` and `npm install` complete without errors.
  - TailwindCSS compiles via `npm run dev`.
- **Definition of Done:**
  - Project runs locally with no warnings/errors.
  - `.env.example` includes all required variables.
  - README.md documents local setup steps.
- **Files Expected:**
  - `composer.json`, `package.json`, `vite.config.js`, `tailwind.config.js`, `.env.example`, `README.md`
- **Suggested Commit Message:** `chore: initialize laravel project with tailwind and vite`

**Status:** `[x]` Done

---

#### Task 1.1.2: Configure Code Quality Tooling

- **Objective:** Set up Pint (Laravel formatter), PHPStan/Larastan for static analysis, and Husky for pre-commit hooks to enforce code standards.
- **Dependencies:** Task 1.1.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - `./vendor/bin/pint` formats code without errors.
  - Static analysis passes at a baseline level.
  - Pre-commit hook prevents committing unformatted code.
- **Definition of Done:**
  - Configuration files exist and are documented.
  - CI pipeline (if configured) runs lint + analysis on every push.
- **Files Expected:**
  - `pint.json`, `phpstan.neon` (or `larastan.neon`), `.husky/pre-commit`
- **Suggested Commit Message:** `chore: add pint, phpstan, and pre-commit hooks`

**Status:** `[x]` Done

---

### Feature 1.2: Database Schema & Migrations

#### Task 1.2.1: Create Users Table Migration with Role Support

- **Objective:** Create the `users` table with all columns defined in erd.md (full_name, email, phone, role enum, domicile, profile_photo_url, is_active, otp_enabled, soft deletes).
- **Dependencies:** Task 1.1.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Migration creates `users` table matching ERD spec.
  - Role column supports: `vehicle_owner`, `workshop`, `super_admin`.
  - Soft delete (`deleted_at`) column present.
  - Email has a unique index.
- **Definition of Done:**
  - `php artisan migrate` runs without errors.
  - `php artisan migrate:rollback` cleanly reverts.
  - User model `$fillable`, `$hidden`, and `$casts` reflect schema.
- **Files Expected:**
  - `database/migrations/xxxx_create_users_table.php`
  - `database/migrations/xxxx_add_role_to_users_table.php`
  - `app/Models/User.php`
- **Suggested Commit Message:** `feat(db): create users table with role enum and soft deletes`

**Status:** `[x]` Done

---

#### Task 1.2.2: Create Workshops Table Migration

- **Objective:** Create the `workshops` table (name, address, latitude, longitude, phone, email, owner_name, owner_ktp, legal_document_url, status enum, rating, operational_hours, soft deletes).
- **Dependencies:** Task 1.2.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Migration creates `workshops` table matching ERD spec.
  - Status enum supports: `pending`, `verified`, `rejected`, `revision_needed`.
  - Foreign key `user_id` references the workshop admin owner.
- **Definition of Done:**
  - Migration runs and rolls back cleanly.
  - Workshop model with relationships to User.
- **Files Expected:**
  - `database/migrations/xxxx_create_workshops_table.php`
  - `app/Models/Workshop.php`
- **Suggested Commit Message:** `feat(db): create workshops table with status enum and geo columns`

**Status:** `[x]` Done

---

#### Task 1.2.3: Create Vehicles Table Migration

- **Objective:** Create the `vehicles` table (owner_id FK, brand, model, year, VIN unique, plate_number unique, color, fuel_type enum, odometer fields, photo_url, health_status enum, oil_life_percentage, soft deletes).
- **Dependencies:** Task 1.2.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Migration creates `vehicles` table matching ERD spec.
  - VIN and plate_number have unique indexes.
  - `owner_id` foreign key references `users.id`.
  - Health status enum: `good`, `needs_service`, `critical`.
  - Fuel type enum: `gasoline`, `diesel`, `electric`, `hybrid`.
- **Definition of Done:**
  - Migration runs and rolls back cleanly.
  - Vehicle model with relationships (belongsTo User, hasMany ServiceRecords).
- **Files Expected:**
  - `database/migrations/xxxx_create_vehicles_table.php`
  - `app/Models/Vehicle.php`
- **Suggested Commit Message:** `feat(db): create vehicles table with VIN/plate uniqueness`

**Status:** `[x]` Done

---

#### Task 1.2.4: Create QR Codes Table Migration

- **Objective:** Create the `qr_codes` table (vehicle_id FK, qr_token unique, status enum, issued_at, revoked_at).
- **Dependencies:** Task 1.2.3
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Migration creates `qr_codes` table matching ERD spec.
  - `qr_token` has a unique index.
  - Status enum: `active`, `inactive`, `revoked`.
  - One-to-one relation with vehicles (one active QR per vehicle).
- **Definition of Done:**
  - Migration runs and rolls back cleanly.
  - QrCode model with belongsTo Vehicle relationship.
- **Files Expected:**
  - `database/migrations/xxxx_create_qr_codes_table.php`
  - `app/Models/QrCode.php`
- **Suggested Commit Message:** `feat(db): create qr_codes table with encrypted token column`

**Status:** `[x]` Done

---

#### Task 1.2.5: Create QR Scan Logs Table Migration

- **Objective:** Create the `qr_scan_logs` table (qr_code_id FK, vehicle_id FK, workshop_id FK, scanned_by_staff_id FK, is_valid_scan, scanned_at).
- **Dependencies:** Task 1.2.4, Task 1.2.2
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Migration creates table with all foreign keys matching ERD.
  - Both valid and invalid scans are recorded.
- **Definition of Done:**
  - Migration runs cleanly.
  - QrScanLog model with relationships.
- **Files Expected:**
  - `database/migrations/xxxx_create_qr_scan_logs_table.php`
  - `app/Models/QrScanLog.php`
- **Suggested Commit Message:** `feat(db): create qr_scan_logs table for audit trail`

**Status:** `[x] Done`

---

#### Task 1.2.6: Create Service Records & Service Parts Tables Migration

- **Objective:** Create `service_records` (vehicle_id, workshop_id, performed_by, service_type, odometer_at_service, mechanic_notes, status, total_cost, service_date, soft deletes) and `service_parts` (service_record_id, part_name, quantity, unit_price, part_category).
- **Dependencies:** Task 1.2.3, Task 1.2.2
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Both tables match ERD spec.
  - Service record status enum: `completed`, `in_progress`.
  - service_parts has FK to service_records.
- **Definition of Done:**
  - Migrations run and roll back cleanly.
  - ServiceRecord model (hasMany ServicePart, belongsTo Vehicle/Workshop).
  - ServicePart model (belongsTo ServiceRecord).
- **Files Expected:**
  - `database/migrations/xxxx_create_service_records_table.php`
  - `database/migrations/xxxx_create_service_parts_table.php`
  - `app/Models/ServiceRecord.php`
  - `app/Models/ServicePart.php`
- **Suggested Commit Message:** `feat(db): create service_records and service_parts tables`

**Status:** `[x] Done`

---

#### Task 1.2.7: Create Workshop Staff Table Migration

- **Objective:** Create `workshop_staff` junction table (workshop_id FK, user_id FK, position enum, is_active, joined_at).
- **Dependencies:** Task 1.2.1, Task 1.2.2
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Position enum: `mechanic`, `admin`.
  - Foreign keys to both users and workshops.
- **Definition of Done:**
  - Migration runs cleanly.
  - WorkshopStaff model with relationships.
- **Files Expected:**
  - `database/migrations/xxxx_create_workshop_staff_table.php`
  - `app/Models/WorkshopStaff.php`
- **Suggested Commit Message:** `feat(db): create workshop_staff junction table`

**Status:** `[x] Done`

---

#### Task 1.2.8: Create Workshop Verification Table Migration

- **Objective:** Create `workshop_verification` table (workshop_id FK, reviewed_by FK, status enum, rejection_reason, reviewed_at).
- **Dependencies:** Task 1.2.2
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Status enum: `pending`, `approved`, `rejected`, `revision_needed`.
  - reviewed_by references super_admin user.
- **Definition of Done:**
  - Migration runs cleanly.
  - WorkshopVerification model with relationships.
- **Files Expected:**
  - `database/migrations/xxxx_create_workshop_verification_table.php`
  - `app/Models/WorkshopVerification.php`
- **Suggested Commit Message:** `feat(db): create workshop_verification table`

**Status:** `[x] Done`

---

#### Task 1.2.9: Create Ownership Transfers Table Migration

- **Objective:** Create `ownership_transfers` table (vehicle_id FK, from_user_id FK, to_user_id FK, status enum, disclaimer_acknowledged, timestamps for each stage, expires_at).
- **Dependencies:** Task 1.2.3, Task 1.2.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Status enum: `pending_recipient`, `approved`, `confirmed`, `rejected`, `expired`.
  - Foreign keys to vehicles and users (from/to).
- **Definition of Done:**
  - Migration runs cleanly.
  - OwnershipTransfer model with relationships.
- **Files Expected:**
  - `database/migrations/xxxx_create_ownership_transfers_table.php`
  - `app/Models/OwnershipTransfer.php`
- **Suggested Commit Message:** `feat(db): create ownership_transfers table with status workflow`

**Status:** `[x] Done`

---

#### Task 1.2.10: Create Notifications Table Migration

- **Objective:** Create `notifications` table (user_id FK, type, title, message, is_read, created_at).
- **Dependencies:** Task 1.2.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - FK to users table.
  - `is_read` defaults to false.
- **Definition of Done:**
  - Migration runs cleanly.
  - Notification model with relationships.
- **Files Expected:**
  - `database/migrations/xxxx_create_notifications_table.php`
  - `app/Models/Notification.php`
- **Suggested Commit Message:** `feat(db): create notifications table`

**Status:** `[x] Done`

---

#### Task 1.2.11: Create Audit Logs Table Migration

- **Objective:** Create `audit_logs` table (actor_user_id FK, action, entity_type, entity_id, metadata JSON, ip_address, created_at). Append-only — no update/delete at app level.
- **Dependencies:** Task 1.2.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Table matches ERD spec.
  - Metadata column is JSON type.
  - No `updated_at` or `deleted_at` columns (append-only).
- **Definition of Done:**
  - Migration runs cleanly.
  - AuditLog model (no SoftDeletes, `$timestamps` adjusted).
- **Files Expected:**
  - `database/migrations/xxxx_create_audit_logs_table.php`
  - `app/Models/AuditLog.php`
- **Suggested Commit Message:** `feat(db): create immutable audit_logs table`

**Status:** `[x] Done`

---

#### Task 1.2.12: Create Database Seeders

- **Objective:** Create seeders for development/testing: SuperAdmin user, sample vehicle owners, sample workshops (verified), sample vehicles, sample service records.
- **Dependencies:** All migration tasks (1.2.1–1.2.11)
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - `php artisan db:seed` populates all tables with realistic test data.
  - At least 1 super_admin, 2 workshops (1 verified, 1 pending), 3 vehicle owners, 5 vehicles, 10 service records.
- **Definition of Done:**
  - Seeders are idempotent (can be re-run safely).
  - Seeded data passes all validation rules.
- **Files Expected:**
  - `database/seeders/DatabaseSeeder.php`
  - `database/seeders/UserSeeder.php`
  - `database/seeders/WorkshopSeeder.php`
  - `database/seeders/VehicleSeeder.php`
  - `database/seeders/ServiceRecordSeeder.php`
- **Suggested Commit Message:** `feat(db): add comprehensive development seeders`

**Status:** `[x]` Done

#### Task 1.3.1: Configure CI Pipeline

- **Objective:** Set up GitHub Actions (or equivalent) to run tests, lint, and static analysis on every push/PR.
- **Dependencies:** Task 1.1.2
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Pipeline runs `php artisan test`, `./vendor/bin/pint --test`, and PHPStan on every PR.
  - Pipeline fails if any step fails.
  - Badge displayed in README.
- **Definition of Done:**
  - `.github/workflows/ci.yml` exists and triggers correctly.
  - At least one successful pipeline run verified.
- **Files Expected:**
  - `.github/workflows/ci.yml`
- **Suggested Commit Message:** `ci: add github actions pipeline for tests and lint`

**Status:** `[x]` Done

---

#### Task 1.3.2: Configure Production Deployment Pipeline

- **Objective:** Set up automated deployment to staging/production using CI/CD.
- **Dependencies:** Task 1.3.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Merge to `main` triggers deployment to staging.
  - Manual approval gate before production deploy.
  - Zero-downtime deployment strategy.
- **Definition of Done:**
  - Deployment pipeline documented.
  - Successful staging deploy verified.
- **Files Expected:**
  - `.github/workflows/deploy.yml`, deployment configuration files
- **Suggested Commit Message:** `ci: add staging and production deployment pipeline`

**Status:** `[~]` Skipped — Pending hosting decision (VPS / PaaS / Cloud). Revisit when infrastructure is confirmed.

---

## EPIC 2: AUTHENTICATION & AUTHORIZATION

> Implement multi-role authentication (Login, Register, Forgot Password, OTP) and role-based access control.

---

### Feature 2.1: Customer Authentication

#### Task 2.1.1: Implement Customer Registration Page & Logic

- **Objective:** Build the registration form (Name, Email, Password, Confirm Password, Role selection) and backend validation/storage.
- **Dependencies:** Task 1.2.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Form validates: required name, valid unique email, password min 8 chars, password confirmation match. (FR-005–FR-008)
  - Role selection between `vehicle_owner` and `workshop` is functional. (FR-005)
  - Duplicate email shows error "Email sudah terdaftar". (FR-008)
  - Successful registration redirects to dashboard. (AC for US-001)
  - Password stored as bcrypt hash. (SEC-003)
- **Definition of Done:**
  - Feature test `test_new_users_can_register` passes.
  - Feature test `test_new_workshop_users_can_register` passes.
  - Both role registrations verified end-to-end.
- **Files Expected:**
  - `app/Http/Controllers/Auth/RegisteredUserController.php`
  - `resources/views/auth/register.blade.php`
  - `tests/Feature/Auth/RegistrationTest.php`
- **Suggested Commit Message:** `feat(auth): implement customer registration with role selection`

**Status:** `[x]` Done

---

#### Task 2.1.2: Implement Login Page & Logic

- **Objective:** Build the login form (Email, Password, Remember Me) with credential validation and role-based redirect.
- **Dependencies:** Task 2.1.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Valid credentials redirect to role-appropriate dashboard. (FR-001, FR-002)
  - Invalid credentials show generic error "Email atau password salah". (AC for US-002)
  - "Remember Me" checkbox extends session. (FR-003)
  - Link to Register page visible. (FR-011)
- **Definition of Done:**
  - Feature tests for login success and failure pass.
  - Role-based redirect verified for all roles.
- **Files Expected:**
  - `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
  - `resources/views/auth/login.blade.php`
  - `tests/Feature/Auth/AuthenticationTest.php`
- **Suggested Commit Message:** `feat(auth): implement login with role-based redirect`

**Status:** `[x]` Done

---

#### Task 2.1.3: Implement Forgot Password & Reset Password

- **Objective:** Allow users to request a password reset link via email and set a new password using the token.
- **Dependencies:** Task 2.1.2
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - User enters email → receives reset link via email. (FR-004)
  - Reset link contains a secure, time-limited token.
  - New password is validated (min 8 chars, confirmed).
  - After reset, user can login with new password.
- **Definition of Done:**
  - Feature tests for password reset flow pass.
  - Email sending verified (using log driver in tests).
- **Files Expected:**
  - `app/Http/Controllers/Auth/PasswordResetLinkController.php`
  - `app/Http/Controllers/Auth/NewPasswordController.php`
  - `resources/views/auth/forgot-password.blade.php`
  - `resources/views/auth/reset-password.blade.php`
  - `tests/Feature/Auth/PasswordResetTest.php`
- **Suggested Commit Message:** `feat(auth): implement forgot/reset password flow`

**Status:** `[x]` Done

---

### Feature 2.2: Workshop Registration & Approval

#### Task 2.2.1: Implement Workshop Registration Form (Multi-step)

- **Objective:** Build the workshop registration form with business data (name, address, services, legal docs), owner data (name, email, phone, KTP), and account password.
- **Dependencies:** Task 1.2.2, Task 2.1.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Multi-step form collects all required data. (FR-079–FR-081)
  - Document upload supports PDF/JPG/PNG, max 10MB. (Validation Rules)
  - On submit, workshop status set to `pending`. (FR-082)
  - User created with role `workshop` and workshop record created.
- **Definition of Done:**
  - Registration creates both user and workshop records.
  - Upload files stored correctly.
  - Feature test for workshop registration passes.
- **Files Expected:**
  - `app/Http/Controllers/Auth/WorkshopRegistrationController.php`
  - `resources/views/auth/register-workshop.blade.php`
  - `tests/Feature/Auth/WorkshopRegistrationTest.php`
- **Suggested Commit Message:** `feat(auth): implement workshop registration multi-step form`

**Status:** `[x]` Done

---

#### Task 2.2.2: Implement Workshop Pending Approval Page

- **Objective:** After workshop registration, show a "Pending Approval" page with status indicator while Super Admin reviews.
- **Dependencies:** Task 2.2.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Workshop users see pending page if status is not `verified`. (FR-083)
  - Page displays current status (Pending/Rejected/Revision Needed).
  - Rejected status shows rejection reason.
  - Middleware prevents access to main dashboard until verified. (FR-078)
- **Definition of Done:**
  - WorkshopApprovedMiddleware blocks unverified workshops.
  - Pending page renders correctly for each status.
- **Files Expected:**
  - `app/Http/Middleware/WorkshopApprovedMiddleware.php`
  - `resources/views/workshop/pending.blade.php`
- **Suggested Commit Message:** `feat(auth): add workshop pending approval page and middleware`

**Status:** `[x]` Done

---

### Feature 2.3: Role-Based Access Control (RBAC)

#### Task 2.3.1: Implement Role Middleware

- **Objective:** Create middleware that restricts route access based on user role, following the Role Permission Matrix (PRD §33).
- **Dependencies:** Task 2.1.2
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - `role:vehicle_owner` middleware allows only vehicle_owner role.
  - `role:workshop` middleware allows only workshop role.
  - `role:super_admin` middleware allows only super_admin role.
  - Unauthorized access returns 403 or redirects to correct dashboard. (ERR-403)
- **Definition of Done:**
  - Middleware registered in `bootstrap/app.php` or kernel.
  - Feature tests verify access control for each role combination.
- **Files Expected:**
  - `app/Http/Middleware/RoleMiddleware.php`
  - `tests/Feature/Middleware/RoleMiddlewareTest.php`
- **Suggested Commit Message:** `feat(auth): implement role-based access control middleware`

**Status:** `[x]` Done

---

#### Task 2.3.2: Implement Super Admin OTP Authentication

- **Objective:** Add OTP verification as a second factor for Super Admin login. (FR-084–FR-087)
- **Dependencies:** Task 2.3.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - After valid username/password, Super Admin is prompted for OTP. (FR-085)
  - OTP is 6-digit numeric, valid for 5 minutes max. (Validation Rules)
  - Invalid/expired OTP denies access. (FR-086)
  - Every login attempt (success/failure) is recorded in audit log. (FR-087)
- **Definition of Done:**
  - OTP generation, delivery (email/SMS), and verification work end-to-end.
  - Audit log entries created for all login attempts.
  - Feature tests cover valid OTP, invalid OTP, and expired OTP.
- **Files Expected:**
  - `app/Http/Controllers/Auth/SuperAdminOtpController.php`
  - `app/Services/OtpService.php`
  - `resources/views/auth/otp-verify.blade.php`
  - `tests/Feature/Auth/SuperAdminOtpTest.php`
- **Suggested Commit Message:** `feat(auth): add OTP two-factor auth for super admin login`

**Status:** `[ ]` To Do

---

#### Task 2.3.3: Implement Rate Limiting on Login Endpoints

- **Objective:** Apply rate limiting to prevent brute-force attacks on login routes. (SEC-005)
- **Dependencies:** Task 2.1.2
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - After 5 failed login attempts within 1 minute, further attempts blocked for 1 minute. (ERR-429)
  - Rate limit applies per IP + email combination.
  - User sees "Terlalu banyak percobaan" error message.
- **Definition of Done:**
  - Rate limiting configured via Laravel's `RateLimiter`.
  - Feature test simulates brute-force and verifies blocking.
- **Files Expected:**
  - `app/Providers/AppServiceProvider.php` (or `RouteServiceProvider`)
  - `tests/Feature/Auth/RateLimitTest.php`
- **Suggested Commit Message:** `feat(security): add rate limiting to login endpoints`

**Status:** `[ ]` To Do

---

## EPIC 3: VEHICLE MANAGEMENT

> Allow vehicle owners to add, view, edit, and manage their vehicles, including QR Code generation.

---

### Feature 3.1: Vehicle CRUD

#### Task 3.1.1: Implement Add Vehicle Page & Backend

- **Objective:** Create the form and backend logic for vehicle owners to register a new vehicle (photo upload, brand, model, year, plate, VIN, color, fuel type, initial odometer).
- **Dependencies:** Task 1.2.3, Task 2.3.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Form collects all required fields per FR-052–FR-055.
  - Photo upload supports JPG/PNG, max 5MB. (Validation Rules)
  - VIN validated as 17 alphanumeric chars, unique. (FR-056)
  - Plate number validated as unique. (FR-056)
  - On save, QR Code is auto-generated. (FR-057)
  - Successful save redirects to QR Code page. (Success State)
- **Definition of Done:**
  - Vehicle creation works end-to-end with QR generation.
  - Validation error messages display correctly.
  - Feature test covers valid submission and duplicate VIN/plate rejection.
- **Files Expected:**
  - `app/Http/Controllers/VehicleController.php` (store method)
  - `resources/views/vehicles/create.blade.php`
  - `app/Http/Requests/StoreVehicleRequest.php`
  - `tests/Feature/Vehicle/CreateVehicleTest.php`
- **Suggested Commit Message:** `feat(vehicle): implement add vehicle form with validation and QR generation`

**Status:** `[ ]` To Do

##### Subtask 3.1.1a: Create Vehicle Photo Upload Handler

- **Objective:** Handle file upload, validation (format, size), and storage for vehicle photos.
- **Dependencies:** Task 1.1.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Accepts JPG/PNG only, max 5MB.
  - Stores to `storage/app/public/vehicles/`.
  - Returns accessible URL path.
- **Definition of Done:** Upload works and files are publicly accessible via `/storage` symlink.
- **Files Expected:** `app/Services/FileUploadService.php`
- **Suggested Commit Message:** `feat(vehicle): add vehicle photo upload handler`

##### Subtask 3.1.1b: Implement VIN & Plate Number Uniqueness Validation

- **Objective:** Create custom validation rules ensuring VIN (17-char alphanumeric) and plate number uniqueness across the system.
- **Dependencies:** Task 1.2.3
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Duplicate VIN returns "VIN sudah terdaftar". (ERR-409)
  - Duplicate plate returns "Plat nomor sudah terdaftar".
  - VIN format validated (17 chars, alphanumeric).
- **Definition of Done:** Validation rules used in StoreVehicleRequest and tested.
- **Files Expected:** `app/Http/Requests/StoreVehicleRequest.php`
- **Suggested Commit Message:** `feat(vehicle): add VIN and plate number validation rules`

---

#### Task 3.1.2: Implement My Vehicles List Page

- **Objective:** Display all vehicles owned by the authenticated user in a card-based grid with search functionality.
- **Dependencies:** Task 3.1.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - All user's vehicles displayed as cards. (FR-019)
  - Each card shows: badge status (Active/Needs Service/Critical), mileage, fuel type, health indicator, oil life. (FR-022–FR-026)
  - Search by name/plate number functional. (FR-020)
  - "Add Vehicle" button visible. (FR-021)
  - Empty state shown when no vehicles. (Empty State spec)
- **Definition of Done:**
  - Page renders with real data from database.
  - Search filters correctly.
  - Responsive on mobile and desktop.
- **Files Expected:**
  - `app/Http/Controllers/VehicleController.php` (index method)
  - `resources/views/vehicles/index.blade.php`
  - `resources/views/vehicles/partials/vehicle-card.blade.php`
- **Suggested Commit Message:** `feat(vehicle): implement my vehicles list page with search`

**Status:** `[ ]` To Do

---

#### Task 3.1.3: Implement Vehicle Detail Page

- **Objective:** Display comprehensive vehicle detail: status overview, stats (total services, total cost, avg interval), service timeline, spareparts list, mechanic notes, QR Code access, transfer access.
- **Dependencies:** Task 3.1.2, Task 1.2.6
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Vehicle status displayed prominently. (FR-027)
  - Statistics section shows service counts and costs. (FR-028)
  - Service timeline in chronological order. (FR-029)
  - Spareparts tab/section listing all replaced parts. (FR-030)
  - Mechanic notes visible per service session. (FR-031)
  - Quick access to QR Code. (FR-032)
  - Quick access to Transfer Ownership. (FR-033)
- **Definition of Done:**
  - All sections render with real data.
  - Page is responsive and uses tab/accordion for sections.
  - Feature test verifies authorization (only owner can view).
- **Files Expected:**
  - `app/Http/Controllers/VehicleController.php` (show method)
  - `resources/views/vehicles/show.blade.php`
  - `resources/views/vehicles/partials/service-timeline.blade.php`
  - `resources/views/vehicles/partials/spareparts-list.blade.php`
- **Suggested Commit Message:** `feat(vehicle): implement vehicle detail page with timeline and stats`

**Status:** `[ ]` To Do

---

#### Task 3.1.4: Implement Edit Vehicle Page & Backend

- **Objective:** Allow vehicle owners to edit vehicle data (except VIN and plate number which are immutable after creation).
- **Dependencies:** Task 3.1.3
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Edit form pre-populates current data.
  - VIN and plate number fields are read-only.
  - Photo can be updated.
  - Validation applies on update.
- **Definition of Done:**
  - Edit and update work end-to-end.
  - Feature test verifies authorization (only owner can edit).
- **Files Expected:**
  - `app/Http/Controllers/VehicleController.php` (edit, update methods)
  - `resources/views/vehicles/edit.blade.php`
  - `app/Http/Requests/UpdateVehicleRequest.php`
- **Suggested Commit Message:** `feat(vehicle): implement edit vehicle page`

**Status:** `[ ]` To Do

---

### Feature 3.2: QR Code Management

#### Task 3.2.1: Implement QR Code Generation Service

- **Objective:** Generate a unique encrypted QR token per vehicle and render it as a QR Code image. (FR-039–FR-042)
- **Dependencies:** Task 1.2.4
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - QR token is unique and encrypted (not sequential ID). (SEC-006)
  - QR Code image generated in PNG format.
  - QR Code contains an encoded URL pointing to the resolve endpoint.
  - Token stored in `qr_codes` table with status `active`.
- **Definition of Done:**
  - Service generates and stores QR codes.
  - Unit test for QR generation and token uniqueness.
- **Files Expected:**
  - `app/Services/QrCodeService.php`
  - `tests/Unit/Services/QrCodeServiceTest.php`
- **Suggested Commit Message:** `feat(qr): implement QR code generation service with encrypted tokens`

**Status:** `[ ]` To Do

---

#### Task 3.2.2: Implement QR Code Display & Download Page

- **Objective:** Display the vehicle's QR Code with supporting info (VIN, Plate, Engine Type) and provide download functionality.
- **Dependencies:** Task 3.2.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - QR Code displayed prominently with vehicle info. (FR-040)
  - Verification status shown (Verified/Unverified). (FR-041)
  - Download button saves PNG/PDF. (FR-042)
  - Security banner about protecting QR Code displayed. (FR-043)
- **Definition of Done:**
  - QR Code page renders correctly.
  - Download generates valid image file.
- **Files Expected:**
  - `app/Http/Controllers/QrCodeController.php`
  - `resources/views/vehicles/qr-code.blade.php`
- **Suggested Commit Message:** `feat(qr): implement QR code display and download page`

**Status:** `[ ]` To Do

---

#### Task 3.2.3: Implement QR Code Regeneration

- **Objective:** Allow vehicle owners to regenerate a new QR Code if the physical one is lost/damaged. Old QR Code is automatically revoked.
- **Dependencies:** Task 3.2.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Old QR Code status changes to `revoked`.
  - New QR Code generated with new token.
  - Revoked QR Code no longer resolves on scan. (ERR-QR02)
- **Definition of Done:**
  - Regeneration works end-to-end.
  - Old QR scans return proper error.
  - Feature test verifies regeneration flow.
- **Files Expected:**
  - `app/Http/Controllers/QrCodeController.php` (regenerate method)
  - `tests/Feature/QrCode/RegenerateQrCodeTest.php`
- **Suggested Commit Message:** `feat(qr): implement QR code regeneration with old code revocation`

**Status:** `[ ]` To Do

---

## EPIC 4: DASHBOARD & MONITORING

> Build role-specific dashboards that provide at-a-glance insights.

---

### Feature 4.1: Customer Dashboard

#### Task 4.1.1: Implement Vehicle Owner Dashboard

- **Objective:** Build the customer dashboard with Hero Vehicle Card, vehicle statistics, recent service history, and quick access to Digital ID.
- **Dependencies:** Task 3.1.2, Task 1.2.6
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Hero Vehicle Card shows primary vehicle summary. (FR-014)
  - Statistics: total vehicles, average health status. (FR-015)
  - Recent service history list. (FR-016)
  - Quick access button to QR Code/Digital ID. (FR-017)
  - Multiple vehicles supported. (FR-018)
  - Sidebar navigation present. (FR-013)
- **Definition of Done:**
  - Dashboard renders with real data for logged-in vehicle owner.
  - Empty state shown for new users with no vehicles.
  - Responsive layout.
- **Files Expected:**
  - `app/Http/Controllers/DashboardController.php`
  - `resources/views/dashboard/partials/vehicle-owner.blade.php`
- **Suggested Commit Message:** `feat(dashboard): implement vehicle owner dashboard with stats`

**Status:** `[ ]` To Do

---

### Feature 4.2: Workshop Dashboard

#### Task 4.2.1: Implement Workshop Admin Dashboard

- **Objective:** Build the workshop admin dashboard with operational stats (daily/monthly service counts, active staff), trending charts, and sparepart usage summary.
- **Dependencies:** Task 1.2.6, Task 1.2.7, Task 2.2.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Service count stats (daily, weekly, monthly). (FR-095)
  - Active staff count displayed.
  - Chart showing vehicles served over time.
  - Top spareparts summary.
  - Quick access to operational report.
- **Definition of Done:**
  - Dashboard renders with real data.
  - Charts functional (using Chart.js or similar).
  - Responsive layout.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/DashboardController.php`
  - `resources/views/dashboard/partials/workshop.blade.php`
- **Suggested Commit Message:** `feat(dashboard): implement workshop admin dashboard with charts`

**Status:** `[ ]` To Do

---

### Feature 4.3: Super Admin Dashboard

#### Task 4.3.1: Implement Super Admin Dashboard

- **Objective:** Build the super admin dashboard with platform-wide statistics (total users by role, total workshops by status, total vehicles), growth charts, system health, and pending verification queue.
- **Dependencies:** Task 1.2.1, Task 1.2.2, Task 1.2.3, Task 1.2.8
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Total users per role displayed. (FR-101)
  - Total workshops by status (verified/pending/rejected).
  - Total vehicles count.
  - Growth graph (registrations per period).
  - System health indicators (if monitoring is set up). (FR-106)
  - Pending verification list with quick action. (FR-102)
- **Definition of Done:**
  - Dashboard renders with real data.
  - Pending verifications clickable.
  - Responsive layout.
- **Files Expected:**
  - `app/Http/Controllers/SuperAdmin/DashboardController.php`
  - `resources/views/dashboard/partials/super-admin.blade.php`
- **Suggested Commit Message:** `feat(dashboard): implement super admin dashboard with platform stats`

**Status:** `[ ]` To Do

---

## EPIC 5: WORKSHOP OPERATIONS (SCAN QR & SERVICE RECORDING)

> Enable workshop staff to scan vehicle QR codes and record service history.

---

### Feature 5.1: QR Code Scanning

#### Task 5.1.1: Implement Browser-Based QR Scanner

- **Objective:** Build a camera-based QR Code scanner using the browser's MediaDevices API for workshop staff. (FR-088)
- **Dependencies:** Task 3.2.1, Task 2.3.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Camera access requested and scanner renders viewfinder.
  - Successful scan resolves QR token and displays vehicle data within 2 seconds. (NFR-002, FR-089)
  - Invalid QR Code shows error "QR Code tidak dikenali". (FR-090, ERR-QR01)
  - Revoked QR Code shows "QR Code ini sudah tidak aktif". (ERR-QR02)
  - Scan event logged in qr_scan_logs. (FR-045)
- **Definition of Done:**
  - Scanner works on mobile and desktop browsers.
  - QR resolution tested with valid, invalid, and revoked codes.
  - Scan logs created in database.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/ScanController.php`
  - `resources/views/workshop/scan.blade.php`
  - `resources/js/qr-scanner.js`
  - `tests/Feature/Workshop/QrScanTest.php`
- **Suggested Commit Message:** `feat(workshop): implement browser-based QR code scanner`

**Status:** `[ ]` To Do

---

#### Task 5.1.2: Implement QR Token Resolution Endpoint

- **Objective:** Create the backend endpoint that resolves a QR token to vehicle data, validates the scanning workshop's verification status, and logs the scan.
- **Dependencies:** Task 1.2.4, Task 1.2.5
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Valid token returns vehicle data + recent service history.
  - Invalid/revoked token returns appropriate error.
  - Unverified workshop attempting scan is rejected. (Edge Case #2)
  - Scan log entry created (valid or invalid).
- **Definition of Done:**
  - Endpoint handles all edge cases.
  - Feature tests cover valid scan, invalid QR, revoked QR, and unverified workshop.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/ScanController.php` (resolve method)
  - `app/Services/QrCodeService.php` (resolve method)
  - `tests/Feature/Workshop/QrResolveTest.php`
- **Suggested Commit Message:** `feat(workshop): implement QR token resolution with scan logging`

**Status:** `[ ]` To Do

---

### Feature 5.2: Service Record Management

#### Task 5.2.1: Implement Add Service Record Form

- **Objective:** After QR scan, allow workshop staff to add a new service record (service type, odometer, spareparts used, oil change, mechanic notes, cost).
- **Dependencies:** Task 5.1.1, Task 1.2.6
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Form accessible after successful QR scan. (FR-091)
  - Dynamic sparepart entry (add/remove rows). (FR-093)
  - Odometer validation: cannot be less than last recorded value. (Edge Case #5, Validation Rules)
  - On save, vehicle health status and oil life auto-update. (FR-025–FR-026)
  - Notification sent to vehicle owner. (FR-111)
- **Definition of Done:**
  - Service record with parts saved correctly.
  - Vehicle stats updated automatically.
  - Owner notification created.
  - Feature test covers full flow.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/ServiceRecordController.php`
  - `resources/views/workshop/service-records/create.blade.php`
  - `app/Http/Requests/StoreServiceRecordRequest.php`
  - `app/Services/VehicleHealthService.php`
  - `tests/Feature/Workshop/CreateServiceRecordTest.php`
- **Suggested Commit Message:** `feat(workshop): implement service record creation with auto health update`

**Status:** `[ ]` To Do

##### Subtask 5.2.1a: Implement Vehicle Health Auto-Update Logic

- **Objective:** Automatically recalculate vehicle health_status and oil_life_percentage after each service record is saved.
- **Dependencies:** Task 1.2.3
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Oil change resets oil_life to 100%.
  - Health status updated based on business rules (time since last service, odometer interval).
- **Definition of Done:** Unit tests verify health calculation logic.
- **Files Expected:** `app/Services/VehicleHealthService.php`, `tests/Unit/Services/VehicleHealthServiceTest.php`
- **Suggested Commit Message:** `feat(vehicle): implement auto health status calculation`

---

#### Task 5.2.2: Implement Edit Service Record (Time-Limited)

- **Objective:** Allow workshop staff to edit a recently created service record within a configurable time window. (FR-092)
- **Dependencies:** Task 5.2.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Edit allowed within 24 hours of creation (configurable).
  - After time window, edit is blocked.
  - Changes logged in audit trail.
- **Definition of Done:**
  - Time-limited edit works correctly.
  - Feature test covers both within-window and expired scenarios.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/ServiceRecordController.php` (edit, update)
  - `resources/views/workshop/service-records/edit.blade.php`
  - `tests/Feature/Workshop/EditServiceRecordTest.php`
- **Suggested Commit Message:** `feat(workshop): implement time-limited service record editing`

**Status:** `[ ]` To Do

---

### Feature 5.3: Workshop Sparepart Management

#### Task 5.3.1: Implement Sparepart CRUD for Workshop

- **Objective:** Allow workshop staff and admin to manage their sparepart catalog (add, edit, list parts with categories and prices).
- **Dependencies:** Task 1.2.6, Task 2.3.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - CRUD operations for spareparts catalog. (FR-093, FR-100)
  - Parts categorized by type.
  - Used in service record form for quick selection.
- **Definition of Done:**
  - Sparepart management page functional.
  - Parts selectable in service record form.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/SparepartController.php`
  - `resources/views/workshop/spareparts/index.blade.php`
  - `resources/views/workshop/spareparts/create.blade.php`
- **Suggested Commit Message:** `feat(workshop): implement sparepart catalog management`

**Status:** `[ ]` To Do

---

### Feature 5.4: Workshop Customer List

#### Task 5.4.1: Implement Workshop Customer Directory

- **Objective:** Display a list of all customers (vehicle owners) whose vehicles have been serviced at this workshop.
- **Dependencies:** Task 5.2.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - List shows customer name, vehicle(s), last service date. (FR-094, FR-098)
  - Search/filter by customer name or plate number.
  - Empty state when no customers yet.
- **Definition of Done:**
  - Customer list renders with real data derived from service records.
  - Responsive layout.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/CustomerController.php`
  - `resources/views/workshop/customers/index.blade.php`
- **Suggested Commit Message:** `feat(workshop): implement customer directory page`

**Status:** `[ ]` To Do

---

## EPIC 6: SERVICE HISTORY & MONITORING

> Allow vehicle owners to view and filter their service history.

---

### Feature 6.1: Service History View

#### Task 6.1.1: Implement Service History Page with Timeline

- **Objective:** Display the full service history for a vehicle in a timeline format with filters.
- **Dependencies:** Task 3.1.3, Task 1.2.6
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Timeline format with chronological order. (FR-034)
  - Each entry shows: date, workshop, service type, cost. (FR-035)
  - Summary statistics (frequency, avg interval). (FR-036)
  - Status badge per entry (Completed, In Progress). (FR-037)
  - Filter by date range and service type. (FR-038)
  - Empty state when no history.
- **Definition of Done:**
  - Timeline renders with real data.
  - Filters work correctly.
  - Responsive design.
- **Files Expected:**
  - `app/Http/Controllers/ServiceHistoryController.php`
  - `resources/views/vehicles/service-history.blade.php`
- **Suggested Commit Message:** `feat(vehicle): implement service history timeline with filters`

**Status:** `[ ]` To Do

---

## EPIC 7: NEARBY WORKSHOP SEARCH

> Enable vehicle owners to find verified workshops near their location using a map.

---

### Feature 7.1: Geospatial Workshop Search

#### Task 7.1.1: Implement Nearby Workshop Backend (Geospatial Query)

- **Objective:** Create the backend endpoint that returns workshops sorted by distance from given coordinates, with filtering support.
- **Dependencies:** Task 1.2.2
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Endpoint accepts latitude, longitude, radius parameters. (FR-046)
  - Returns workshops sorted by distance.
  - Filter by rating, service type, verification status. (FR-047)
  - Only verified workshops returned to customers. (FR-051)
- **Definition of Done:**
  - Geospatial query efficient (Haversine formula or spatial index).
  - Feature test with seeded workshop data verifies distance sorting.
- **Files Expected:**
  - `app/Http/Controllers/WorkshopSearchController.php`
  - `app/Services/WorkshopSearchService.php`
  - `tests/Feature/Workshop/NearbySearchTest.php`
- **Suggested Commit Message:** `feat(workshop): implement geospatial nearby workshop search`

**Status:** `[ ]` To Do

---

#### Task 7.1.2: Implement Nearby Workshop Map Page

- **Objective:** Build the frontend page with interactive map (Google Maps/Mapbox) showing workshop pins, and a companion workshop card list.
- **Dependencies:** Task 7.1.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Interactive map with workshop pins. (FR-048)
  - Workshop cards with name, distance, rating. (FR-049)
  - "Verified Partner" badge shown. (FR-051)
  - "Directions" button opens external maps app or in-page directions. (FR-050)
  - Location permission requested from user.
  - Filter controls (distance, rating, service type). (FR-047)
  - Empty state if no workshops found.
- **Definition of Done:**
  - Map renders with pins from API data.
  - Cards sync with map viewport.
  - Directions button works.
  - Responsive on mobile.
- **Files Expected:**
  - `app/Http/Controllers/WorkshopSearchController.php` (view method)
  - `resources/views/workshops/nearby.blade.php`
  - `resources/js/workshop-map.js`
- **Suggested Commit Message:** `feat(workshop): implement nearby workshop map page with directions`

**Status:** `[ ]` To Do

---

## EPIC 8: OWNERSHIP TRANSFER

> Enable vehicle owners to transfer vehicle ownership to another registered user with full audit trail.

---

### Feature 8.1: Transfer Initiation & Workflow

#### Task 8.1.1: Implement Transfer Initiation Form

- **Objective:** Build the form where the current owner enters the recipient's email/phone to start a transfer. (FR-063–FR-065)
- **Dependencies:** Task 3.1.3, Task 1.2.9
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Vehicle summary displayed. (FR-063)
  - Recipient identified by email or phone. (FR-064)
  - System verifies recipient has a Maintify account. (FR-065)
  - If recipient not found, suggest inviting them to register.
  - Transfer record created with status `pending_recipient`.
- **Definition of Done:**
  - Transfer initiation creates record in ownership_transfers.
  - Notification sent to recipient.
  - Feature test covers valid and invalid recipient scenarios.
- **Files Expected:**
  - `app/Http/Controllers/OwnershipTransferController.php`
  - `resources/views/vehicles/transfer/initiate.blade.php`
  - `app/Http/Requests/InitiateTransferRequest.php`
  - `tests/Feature/Transfer/InitiateTransferTest.php`
- **Suggested Commit Message:** `feat(transfer): implement transfer initiation with recipient verification`

**Status:** `[ ]` To Do

---

#### Task 8.1.2: Implement Transfer Review & Confirmation

- **Objective:** Build the review page (with disclaimer) and final confirmation flow for both parties. (FR-066–FR-070)
- **Dependencies:** Task 8.1.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Recipient can approve or reject the transfer. (FR-068)
  - Review page shows full vehicle data and disclaimer. (FR-066, FR-067)
  - Disclaimer states transfer is permanent and irreversible.
  - Final confirmation by owner executes the transfer atomically.
  - Owner's access revoked after transfer. (FR-069)
  - All service history preserved. (FR-070)
  - Transfer auto-expires after 7 days if no recipient response. (Edge Case #3)
- **Definition of Done:**
  - Full transfer workflow works end-to-end.
  - Atomic transaction ensures data integrity.
  - Audit log entry created.
  - Both parties notified. (FR-074)
  - Feature test covers approve, reject, and expiry scenarios.
- **Files Expected:**
  - `app/Http/Controllers/OwnershipTransferController.php` (approve, confirm methods)
  - `resources/views/vehicles/transfer/review.blade.php`
  - `resources/views/vehicles/transfer/success.blade.php`
  - `app/Services/OwnershipTransferService.php`
  - `tests/Feature/Transfer/TransferWorkflowTest.php`
- **Suggested Commit Message:** `feat(transfer): implement full transfer review, confirmation, and execution`

**Status:** `[ ]` To Do

---

#### Task 8.1.3: Implement Transfer Expiry Job

- **Objective:** Create a scheduled job that automatically expires pending transfers older than 7 days. (Edge Case #3)
- **Dependencies:** Task 8.1.2
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Job runs daily (or hourly).
  - Transfers with status `pending_recipient` older than 7 days set to `expired`.
  - Both parties notified of expiry.
- **Definition of Done:**
  - Scheduled command registered in `Console/Kernel.php`.
  - Unit test verifies expiry logic.
- **Files Expected:**
  - `app/Console/Commands/ExpireTransfersCommand.php`
  - `tests/Unit/Commands/ExpireTransfersCommandTest.php`
- **Suggested Commit Message:** `feat(transfer): add scheduled job for transfer expiry`

**Status:** `[ ]` To Do

---

## EPIC 9: WORKSHOP ADMINISTRATION

> Provide workshop admins with tools to manage staff, customers, data, and reports.

---

### Feature 9.1: Staff Management

#### Task 9.1.1: Implement Workshop Staff CRUD

- **Objective:** Allow workshop admins to add, edit, activate/deactivate staff accounts. (FR-096)
- **Dependencies:** Task 1.2.7, Task 2.3.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Admin can add new staff (creates user with `workshop` role linked to workshop).
  - Admin can deactivate staff accounts.
  - Admin can edit staff details.
  - Staff list shows name, position, active status, join date.
  - Empty state when no staff.
- **Definition of Done:**
  - CRUD operations work end-to-end.
  - Deactivated staff cannot login.
  - Feature test covers add, edit, deactivate.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/StaffController.php`
  - `resources/views/workshop/staff/index.blade.php`
  - `resources/views/workshop/staff/create.blade.php`
  - `resources/views/workshop/staff/edit.blade.php`
  - `tests/Feature/Workshop/StaffManagementTest.php`
- **Suggested Commit Message:** `feat(workshop): implement staff management CRUD`

**Status:** `[ ]` To Do

---

### Feature 9.2: Workshop Profile Management

#### Task 9.2.1: Implement Workshop Profile Edit

- **Objective:** Allow workshop admins to manage their workshop profile (operational hours, address, contact, services offered). (FR-099)
- **Dependencies:** Task 2.2.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Edit form for workshop details (hours, address, phone, email).
  - Changes reflected on nearby workshop search results.
- **Definition of Done:**
  - Profile update works and validates correctly.
  - Feature test verifies update.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/ProfileController.php`
  - `resources/views/workshop/profile/edit.blade.php`
- **Suggested Commit Message:** `feat(workshop): implement workshop profile management`

**Status:** `[ ]` To Do

---

### Feature 9.3: Operational Reports

#### Task 9.3.1: Implement Workshop Operational Report & Export

- **Objective:** Generate and display operational reports (service counts, top spareparts, revenue if tracked) with download capability. (FR-100)
- **Dependencies:** Task 5.2.1, Task 5.3.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Report shows: total services per period, sparepart usage ranking, revenue summary.
  - Date range filter.
  - Export as CSV or PDF.
- **Definition of Done:**
  - Report page renders with real aggregated data.
  - Export generates valid downloadable file.
- **Files Expected:**
  - `app/Http/Controllers/Workshop/ReportController.php`
  - `resources/views/workshop/reports/index.blade.php`
  - `app/Services/ReportExportService.php`
- **Suggested Commit Message:** `feat(workshop): implement operational reports with export`

**Status:** `[ ]` To Do

---

## EPIC 10: SUPER ADMIN PLATFORM MANAGEMENT

> Equip the Super Admin with tools to verify workshops, manage all users/vehicles/workshops, view audit logs, and configure global settings.

---

### Feature 10.1: Workshop Verification

#### Task 10.1.1: Implement Workshop Verification Queue

- **Objective:** Build the Super Admin interface to review pending workshop registrations and approve/reject/request revision. (FR-102)
- **Dependencies:** Task 2.2.1, Task 1.2.8, Task 4.3.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - List of pending workshops with submitted documents.
  - Super Admin can view details (legal docs, owner info).
  - Actions: Approve, Reject (with reason), Request Revision.
  - On approve, workshop status → `verified`, admin account activated. (Workshop Verification Workflow §30)
  - On reject, rejection reason required and notification sent.
  - All decisions recorded in audit log. (FR-108)
- **Definition of Done:**
  - Full verification workflow works end-to-end.
  - Notifications sent to workshop.
  - Audit log entries created.
  - Feature test covers approve, reject, and revision scenarios.
- **Files Expected:**
  - `app/Http/Controllers/SuperAdmin/WorkshopVerificationController.php`
  - `resources/views/super-admin/workshops/pending.blade.php`
  - `resources/views/super-admin/workshops/review.blade.php`
  - `tests/Feature/SuperAdmin/WorkshopVerificationTest.php`
- **Suggested Commit Message:** `feat(admin): implement workshop verification queue with audit logging`

**Status:** `[ ]` To Do

---

### Feature 10.2: User & Data Management

#### Task 10.2.1: Implement User Management Module

- **Objective:** Allow Super Admin to view, search, and manage all platform users. (FR-103)
- **Dependencies:** Task 4.3.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Paginated user list with search by name/email/role.
  - View user details.
  - Activate/deactivate user accounts.
  - Filter by role.
- **Definition of Done:**
  - User management page functional with pagination.
  - Actions (activate/deactivate) work correctly.
- **Files Expected:**
  - `app/Http/Controllers/SuperAdmin/UserController.php`
  - `resources/views/super-admin/users/index.blade.php`
  - `resources/views/super-admin/users/show.blade.php`
- **Suggested Commit Message:** `feat(admin): implement user management module`

**Status:** `[ ]` To Do

---

#### Task 10.2.2: Implement Vehicle Management Module

- **Objective:** Allow Super Admin to view and manage all vehicles in the system. (FR-104)
- **Dependencies:** Task 4.3.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Paginated vehicle list with search by VIN/plate/owner.
  - View vehicle details including service history.
  - Read-only access (Super Admin doesn't own vehicles).
- **Definition of Done:**
  - Vehicle management page functional.
- **Files Expected:**
  - `app/Http/Controllers/SuperAdmin/VehicleController.php`
  - `resources/views/super-admin/vehicles/index.blade.php`
- **Suggested Commit Message:** `feat(admin): implement vehicle management module`

**Status:** `[ ]` To Do

---

#### Task 10.2.3: Implement Workshop Management Module

- **Objective:** Allow Super Admin to view and manage all workshops (verified, pending, rejected). (FR-105)
- **Dependencies:** Task 10.1.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Paginated workshop list with search and status filter.
  - View workshop details (staff, service stats).
  - Ability to change workshop status.
- **Definition of Done:**
  - Workshop management page functional.
- **Files Expected:**
  - `app/Http/Controllers/SuperAdmin/WorkshopController.php`
  - `resources/views/super-admin/workshops/index.blade.php`
  - `resources/views/super-admin/workshops/show.blade.php`
- **Suggested Commit Message:** `feat(admin): implement workshop management module`

**Status:** `[ ]` To Do

---

### Feature 10.3: Audit Log & Global Settings

#### Task 10.3.1: Implement Audit Log Viewer

- **Objective:** Build the audit log viewer for Super Admin to review all recorded system activities. (FR-108)
- **Dependencies:** Task 1.2.11
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Paginated audit log list with timestamps, actor, action, entity.
  - Filter by date range, actor, action type, entity type.
  - View detailed metadata for each entry.
  - Logs are read-only (no edit/delete). (NFR-017)
- **Definition of Done:**
  - Audit log viewer renders with real data.
  - Filters work correctly.
  - No edit/delete UI exposed.
- **Files Expected:**
  - `app/Http/Controllers/SuperAdmin/AuditLogController.php`
  - `resources/views/super-admin/audit-logs/index.blade.php`
- **Suggested Commit Message:** `feat(admin): implement audit log viewer with filters`

**Status:** `[ ]` To Do

---

#### Task 10.3.2: Implement Global Settings Page

- **Objective:** Provide Super Admin with a page to configure platform-wide settings (service reminder intervals, transfer expiry duration, etc.). (FR-107)
- **Dependencies:** Task 4.3.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Configurable parameters: service reminder interval (days), service reminder mileage (km), transfer expiry days.
  - Settings persisted in database or config.
  - Changes take effect immediately.
- **Definition of Done:**
  - Settings page functional.
  - Changed settings reflected in relevant business logic.
- **Files Expected:**
  - `app/Http/Controllers/SuperAdmin/SettingsController.php`
  - `resources/views/super-admin/settings/index.blade.php`
  - `app/Models/SystemSetting.php` (or config-based)
- **Suggested Commit Message:** `feat(admin): implement global platform settings page`

**Status:** `[ ]` To Do

---

## EPIC 11: NOTIFICATIONS & REMINDERS

> Implement the notification system for service reminders, transfer alerts, and workshop verification updates.

---

### Feature 11.1: Notification Infrastructure

#### Task 11.1.1: Implement Notification Service & Bell Icon UI

- **Objective:** Build the core notification service (create, list, mark as read) and the bell icon with unread count in the navigation bar. (FR-109–FR-113)
- **Dependencies:** Task 1.2.10
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Bell icon shows unread notification count.
  - Dropdown/page shows notification list (title, message, time ago, read status).
  - Click marks as read.
  - Empty state when no notifications.
- **Definition of Done:**
  - Notification CRUD service works.
  - Bell icon renders in all authenticated layouts.
  - Feature test for create and mark-as-read.
- **Files Expected:**
  - `app/Services/NotificationService.php`
  - `app/Http/Controllers/NotificationController.php`
  - `resources/views/components/notification-bell.blade.php`
  - `resources/views/notifications/index.blade.php`
- **Suggested Commit Message:** `feat(notification): implement notification service and bell icon UI`

**Status:** `[ ]` To Do

---

### Feature 11.2: Service Reminders

#### Task 11.2.1: Implement Service Reminder Scheduler

- **Objective:** Create scheduled jobs that send service reminders based on time intervals and mileage thresholds. (FR-109, FR-110)
- **Dependencies:** Task 11.1.1, Task 1.2.6
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Time-based reminder: triggers when interval since last service exceeds threshold (e.g., 3 months).
  - Mileage-based reminder: triggers when current odometer nears threshold km since last service.
  - Reminders delivered as in-app notifications (and optionally email).
  - Duplicate reminders not sent for the same service window.
- **Definition of Done:**
  - Scheduled commands registered and tested.
  - Unit tests verify reminder logic edge cases.
- **Files Expected:**
  - `app/Console/Commands/SendServiceRemindersCommand.php`
  - `app/Services/ServiceReminderService.php`
  - `tests/Unit/Services/ServiceReminderServiceTest.php`
- **Suggested Commit Message:** `feat(notification): implement scheduled service reminder jobs`

**Status:** `[ ]` To Do

---

## EPIC 12: USER SETTINGS & PROFILE

> Allow users to manage their profile, account, and notification preferences.

---

### Feature 12.1: Profile Management

#### Task 12.1.1: Implement Profile Edit Page

- **Objective:** Allow users to update their name, photo, and contact information. (FR-058)
- **Dependencies:** Task 2.1.2
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Edit name, profile photo, phone number.
  - Photo upload (JPG/PNG, max 5MB).
  - Validation and success feedback.
- **Definition of Done:**
  - Profile update works end-to-end.
  - Feature test verifies update.
- **Files Expected:**
  - `app/Http/Controllers/ProfileController.php`
  - `resources/views/profile/edit.blade.php`
- **Suggested Commit Message:** `feat(profile): implement profile edit page`

**Status:** `[ ]` To Do

---

#### Task 12.1.2: Implement Account Settings (Password & Email Change)

- **Objective:** Allow users to change their password and email. (FR-059)
- **Dependencies:** Task 12.1.1
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Password change requires current password verification.
  - Email change requires re-verification.
  - Success notification shown. (Success State)
- **Definition of Done:**
  - Password and email update work correctly.
  - Feature tests pass.
- **Files Expected:**
  - `app/Http/Controllers/ProfileController.php` (or dedicated AccountController)
  - `tests/Feature/ProfileTest.php`
- **Suggested Commit Message:** `feat(profile): implement password and email change`

**Status:** `[ ]` To Do

---

#### Task 12.1.3: Implement Notification Preferences

- **Objective:** Allow users to toggle notification settings (enable/disable service reminders, email notifications). (FR-061)
- **Dependencies:** Task 11.1.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Toggle for in-app service reminders.
  - Toggle for email notifications.
  - Preferences persisted per user.
- **Definition of Done:**
  - Preferences saved and respected by notification service.
- **Files Expected:**
  - `resources/views/profile/partials/notification-preferences.blade.php`
  - `app/Http/Controllers/ProfileController.php`
- **Suggested Commit Message:** `feat(profile): implement notification preference toggles`

**Status:** `[ ]` To Do

---

## EPIC 13: CROSS-CUTTING CONCERNS

> UI states, input validation, security hardening, responsiveness, and documentation.

---

### Feature 13.1: UI State Management

#### Task 13.1.1: Implement Empty, Loading, Success, and Failure States

- **Objective:** Implement consistent UI states across all modules per PRD §36–39.
- **Dependencies:** All feature tasks
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - Skeleton loaders on all data-fetching pages. (Loading State)
  - Empty state illustrations + CTAs on empty lists. (Empty State)
  - Success toast/notification on all create/update actions. (Success State)
  - Error messages on validation failures. (Failure State)
  - Submit buttons disabled during form submission.
- **Definition of Done:**
  - All pages reviewed for state consistency.
  - Verified on both mobile and desktop.
- **Files Expected:**
  - `resources/views/components/empty-state.blade.php`
  - `resources/views/components/loading-skeleton.blade.php`
  - `resources/views/components/toast-notification.blade.php`
- **Suggested Commit Message:** `feat(ui): implement consistent empty, loading, success, and failure states`

**Status:** `[ ]` To Do

---

### Feature 13.2: Input Validation & Security

#### Task 13.2.1: Implement Global Input Validation & Sanitization

- **Objective:** Ensure all form inputs are validated and sanitized to prevent SQL Injection and XSS. (SEC-012, Validation Rules §35)
- **Dependencies:** All form-related tasks
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - All Form Requests implement validation rules from PRD §35.
  - HTML entities escaped in all Blade output.
  - CSRF protection on all forms. (SEC-007)
  - File uploads validated for type, size, and content.
- **Definition of Done:**
  - Security review completed.
  - No unvalidated input reaches the database.
- **Files Expected:**
  - All `app/Http/Requests/*.php` files
- **Suggested Commit Message:** `feat(security): enforce comprehensive input validation across all forms`

**Status:** `[ ]` To Do

---

#### Task 13.2.2: Implement Session Timeout for Admin Roles

- **Objective:** Auto-logout Admin Bengkel and Super Admin after a period of inactivity. (SEC-011)
- **Dependencies:** Task 2.3.1
- **Estimated Complexity:** Low
- **Acceptance Criteria:**
  - Session expires after 30 minutes of inactivity for admin roles.
  - User redirected to login with "Sesi Anda telah berakhir" message. (ERR-401)
- **Definition of Done:**
  - Middleware or session config enforces timeout.
  - Feature test verifies session expiry.
- **Files Expected:**
  - `app/Http/Middleware/SessionTimeoutMiddleware.php`
- **Suggested Commit Message:** `feat(security): implement session timeout for admin roles`

**Status:** `[ ]` To Do

---

### Feature 13.3: Responsive Design & Cross-Browser

#### Task 13.3.1: Implement Full Responsive Testing & Fixes

- **Objective:** Verify and fix responsive layouts across desktop, tablet, and mobile for all pages. (NFR-008)
- **Dependencies:** All UI tasks
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - All pages usable on 320px–1920px viewport widths.
  - Touch targets appropriately sized on mobile.
  - Navigation adapts (sidebar → hamburger on mobile).
- **Definition of Done:**
  - Manual testing on Chrome, Firefox, Safari, Edge. (NFR-012)
  - Critical layout issues fixed.
- **Files Expected:**
  - Various Blade and CSS files
- **Suggested Commit Message:** `fix(ui): resolve responsive layout issues across all pages`

**Status:** `[ ]` To Do

---

### Feature 13.4: API Documentation

#### Task 13.4.1: Generate API Documentation

- **Objective:** Document all API endpoints using OpenAPI/Swagger. (PRD §26)
- **Dependencies:** All backend controller tasks
- **Estimated Complexity:** Medium
- **Acceptance Criteria:**
  - All endpoints documented with request/response schemas.
  - Authentication requirements specified per endpoint.
  - Example requests and responses included.
- **Definition of Done:**
  - Swagger UI accessible at `/api/documentation`.
  - Documentation covers all endpoints from PRD §26.
- **Files Expected:**
  - OpenAPI spec file or Swagger annotations in controllers
- **Suggested Commit Message:** `docs: generate OpenAPI documentation for all endpoints`

**Status:** `[ ]` To Do

---

## EPIC 14: TESTING & QUALITY ASSURANCE

> Comprehensive testing across all modules.

---

### Feature 14.1: Automated Testing

#### Task 14.1.1: Write Unit Tests for All Services

- **Objective:** Ensure all service classes have comprehensive unit test coverage.
- **Dependencies:** All service class tasks
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - QrCodeService, VehicleHealthService, OwnershipTransferService, NotificationService, ServiceReminderService all have 80%+ coverage.
  - Edge cases covered (invalid inputs, boundary conditions).
- **Definition of Done:**
  - `php artisan test --coverage` reports 80%+ on service layer.
- **Files Expected:**
  - `tests/Unit/Services/*.php`
- **Suggested Commit Message:** `test: add comprehensive unit tests for all service classes`

**Status:** `[ ]` To Do

---

#### Task 14.1.2: Write Feature Tests for All Endpoints

- **Objective:** Cover all web routes and API endpoints with feature/integration tests.
- **Dependencies:** All controller tasks
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - Each controller method has at least one success and one failure test.
  - Authorization tested (wrong role → 403).
  - Validation tested (invalid input → 422).
- **Definition of Done:**
  - `php artisan test` passes with all feature tests.
- **Files Expected:**
  - `tests/Feature/**/*.php`
- **Suggested Commit Message:** `test: add comprehensive feature tests for all routes`

**Status:** `[ ]` To Do

---

### Feature 14.2: Security Testing

#### Task 14.2.1: Perform Basic Penetration Testing

- **Objective:** Run basic security testing to verify RBAC, CSRF protection, SQL injection prevention, and XSS prevention.
- **Dependencies:** Task 13.2.1, Task 2.3.1
- **Estimated Complexity:** High
- **Acceptance Criteria:**
  - No SQL injection vulnerabilities found.
  - No XSS vulnerabilities found.
  - RBAC enforced on all sensitive endpoints.
  - CSRF tokens validated on all state-changing requests.
- **Definition of Done:**
  - Security audit report generated.
  - All critical/high findings resolved.
- **Files Expected:**
  - Security audit report document
- **Suggested Commit Message:** `security: resolve findings from penetration testing`

**Status:** `[ ]` To Do

---

## Summary — Task Count by Status

| Status | Count |
|---|---|
| `[x]` Done | 17 |
| `[/]` In Progress | 0 |
| `[ ]` To Do | 37 |
| **Total Tasks** | **54** |

> **Note:** Subtasks are counted separately within their parent tasks. The 54 count reflects top-level tasks only. This task list is a living document and will be updated as development progresses.
