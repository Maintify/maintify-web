<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnershipTransfer extends Model
{
    use HasFactory;

    protected $table = 'ownership_transfers';

    /**
     * Status constants matching the workflow stages.
     */
    const STATUS_PENDING_RECIPIENT = 'pending_recipient';
    const STATUS_APPROVED = 'approved';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'vehicle_id',
        'from_user_id',
        'to_user_id',
        'status',
        'disclaimer_acknowledged',
        'requested_at',
        'approved_at',
        'confirmed_at',
        'expires_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // =========================================================
    // Relationships
    // =========================================================

    /**
     * Kendaraan yang ditransfer.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * User yang mengirim/melepas kendaraan (pemilik saat ini).
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * User yang menerima kendaraan (calon pemilik baru).
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
