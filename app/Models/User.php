<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property-read Workshop|null $workshop
 * @property-read WorkshopStaff|null $workshopStaff
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // =========================================================
    // Role Constants
    // =========================================================

    const ROLE_SUPER_ADMIN = 'super_admin';

    const ROLE_WORKSHOP = 'workshop';

    const ROLE_VEHICLE_OWNER = 'vehicle_owner';

    /**
     * All available roles.
     */
    const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_WORKSHOP => 'Bengkel Mitra',
        self::ROLE_VEHICLE_OWNER => 'Pemilik Kendaraan',
    ];

    // =========================================================
    // Mass Assignable Attributes
    // =========================================================

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'photo_url',
        'enable_service_reminders',
        'enable_email_notifications',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'enable_service_reminders' => 'boolean',
            'enable_email_notifications' => 'boolean',
        ];
    }

    // =========================================================
    // Role Helper Methods
    // =========================================================

    /**
     * Check if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * Check if the user is a Workshop (Bengkel Mitra).
     */
    public function isWorkshop(): bool
    {
        return $this->role === self::ROLE_WORKSHOP;
    }

    /**
     * Check if the user is a Vehicle Owner (Pemilik Kendaraan).
     */
    public function isVehicleOwner(): bool
    {
        return $this->role === self::ROLE_VEHICLE_OWNER;
    }

    /**
     * Check if the user has a given role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the given roles.
     *
     * @param  array<string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get the human-readable role label.
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Unknown';
    }

    // =========================================================
    // Relationships
    // =========================================================

    /**
     * Kendaraan milik user (role: vehicle_owner).
     *
     * @return HasMany<Vehicle, $this>
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Bengkel yang dikelola user (role: workshop).
     *
     * @return HasOne<Workshop, $this>
     */
    public function workshop(): HasOne
    {
        return $this->hasOne(Workshop::class);
    }

    /**
     * Audit log dari aksi yang dilakukan user ini.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_user_id');
    }

    /**
     * Semua log scan QR Code yang dilakukan oleh staff/user ini.
     */
    public function scanLogs(): HasMany
    {
        return $this->hasMany(QrScanLog::class, 'scanned_by_staff_id');
    }

    /**
     * Data keanggotaan staff bengkel untuk user ini.
     */
    public function workshopStaff(): HasOne
    {
        return $this->hasOne(WorkshopStaff::class);
    }

    /**
     * Semua review/verifikasi bengkel yang dilakukan oleh super admin (user ini).
     */
    public function workshopReviews(): HasMany
    {
        return $this->hasMany(WorkshopVerification::class, 'reviewed_by');
    }

    /**
     * Transfer kepemilikan yang dikirim oleh user ini (sebagai pengirim).
     */
    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(OwnershipTransfer::class, 'from_user_id');
    }

    /**
     * Transfer kepemilikan yang diterima oleh user ini (sebagai penerima).
     */
    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(OwnershipTransfer::class, 'to_user_id');
    }

    /**
     * Semua notifikasi in-app untuk user ini.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
