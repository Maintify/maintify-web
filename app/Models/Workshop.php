<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workshop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'description',
        'logo_url',
        'is_active',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    // =========================================================
    // Relationships
    // =========================================================

    /**
     * Akun user yang mengelola bengkel ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Semua record service yang dikerjakan bengkel ini.
     */
    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    /**
     * Semua log scan QR Code di bengkel ini.
     */
    public function scanLogs(): HasMany
    {
        return $this->hasMany(QrScanLog::class);
    }

    /**
     * Semua staf yang bekerja di bengkel ini.
     */
    public function staff(): HasMany
    {
        return $this->hasMany(WorkshopStaff::class);
    }

    /**
     * Data verifikasi untuk bengkel ini.
     */
    public function verification(): HasOne
    {
        return $this->hasOne(WorkshopVerification::class);
    }

    // =========================================================
    // Scopes
    // =========================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Lokasi lengkap bengkel.
     */
    public function getFullAddressAttribute(): string
    {
        return collect([$this->address, $this->city, $this->province, $this->postal_code])
            ->filter()
            ->implode(', ');
    }
}
