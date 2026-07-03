<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePart extends Model
{
    use HasFactory;

    protected $table = 'service_parts';

    protected $fillable = [
        'service_record_id',
        'part_name',
        'quantity',
        'unit_price',
        'part_category',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    // =========================================================
    // Relationships
    // =========================================================

    public function serviceRecord(): BelongsTo
    {
        return $this->belongsTo(ServiceRecord::class);
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Get the subtotal for this part (quantity * unit price).
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Format subtotal to Rupiah.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }
}
