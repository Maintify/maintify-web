<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'plate_number',
        'brand',
        'model',
        'type',
        'year',
        'color',
        'engine_number',
        'chassis_number',
        'current_odometer',
        'next_service_odometer',
        'next_service_date',
        'qr_code',
        'qr_code_url',
        'photo_url',
        'health_status',
        'health_score',
        'is_active',
    ];

    protected $casts = [
        'next_service_date' => 'date',
        'is_active' => 'boolean',
        'health_score' => 'integer',
        'current_odometer' => 'integer',
    ];

    // =========================================================
    // Boot — generate QR code token otomatis
    // =========================================================

    protected static function booted(): void
    {
        static::creating(function (Vehicle $vehicle) {
            if (empty($vehicle->qr_code)) {
                $vehicle->qr_code = strtoupper(Str::random(12));
            }
        });
    }

    // =========================================================
    // Relationships
    // =========================================================

    /**
     * Pemilik kendaraan.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Semua histori service kendaraan ini.
     */
    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }

    /**
     * Histori service terbaru.
     */
    public function latestService()
    {
        return $this->hasOne(ServiceRecord::class)->latestOfMany('service_date');
    }

    /**
     * Semua QR Code kendaraan ini.
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * QR Code aktif kendaraan ini.
     */
    public function activeQrCode()
    {
        return $this->hasOne(QrCode::class)->where('status', QrCode::STATUS_ACTIVE);
    }

    /**
     * Semua log scan QR Code untuk kendaraan ini.
     */
    public function scanLogs(): HasMany
    {
        return $this->hasMany(QrScanLog::class);
    }

    /**
     * Semua transfer kepemilikan untuk kendaraan ini.
     */
    public function ownershipTransfers(): HasMany
    {
        return $this->hasMany(OwnershipTransfer::class);
    }

    // =========================================================
    // Scopes
    // =========================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNeedsService($query)
    {
        return $query->where('health_status', '!=', 'good');
    }

    public function scopeByOwner($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Nama lengkap kendaraan: Merek + Model + Tahun.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} ({$this->year})";
    }

    /**
     * Apakah kendaraan butuh service berdasarkan odometer.
     */
    public function isDueForService(): bool
    {
        if ($this->next_service_odometer && $this->current_odometer >= $this->next_service_odometer) {
            return true;
        }
        if ($this->next_service_date && $this->next_service_date->isPast()) {
            return true;
        }

        return false;
    }

    /**
     * Label warna untuk health_status.
     */
    public function getHealthBadgeColorAttribute(): string
    {
        return match ($this->health_status) {
            'good' => 'green',
            'warning' => 'yellow',
            'critical' => 'red',
        };
    }
}
