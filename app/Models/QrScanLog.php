<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrScanLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'qr_code_id',
        'vehicle_id',
        'workshop_id',
        'scanned_by_staff_id',
        'is_valid_scan',
        'scanned_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_valid_scan' => 'boolean',
        'scanned_at' => 'datetime',
    ];

    // =========================================================
    // Relationships
    // =========================================================

    /**
     * Get the QR code associated with this scan log.
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class);
    }

    /**
     * Get the vehicle associated with this scan log.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the workshop where the scan occurred.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Get the staff member who performed the scan.
     */
    public function scannedByStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by_staff_id');
    }
}
