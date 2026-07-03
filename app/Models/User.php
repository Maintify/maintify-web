<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'password',
        'role',
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
     */
    public function workshop(): HasOne
    {
        return $this->hasOne(Workshop::class);
    }

    /**
     * Log aktivitas user.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
