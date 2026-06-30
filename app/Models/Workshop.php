<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active'   => 'boolean',
        'approved_at' => 'datetime',
    ];

    const STATUS_PENDING  = 'pending';
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
     * Semua histori service yang dikerjakan bengkel ini.
     */
    public function serviceHistories(): HasMany
    {
        return $this->hasMany(ServiceHistory::class);
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
