<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceHistory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Service type constants.
     */
    const TYPE_OIL_CHANGE = 'oil_change';

    const TYPE_TUNE_UP = 'tune_up';

    const TYPE_PERIODIC_SERVICE = 'periodic_service';

    const TYPE_REPAIR = 'repair';

    const TYPE_TIRE_CHANGE = 'tire_change';

    const TYPE_BRAKE_SERVICE = 'brake_service';

    const TYPE_OTHER = 'other';

    const SERVICE_TYPES = [
        self::TYPE_OIL_CHANGE => 'Ganti Oli',
        self::TYPE_TUNE_UP => 'Tune Up',
        self::TYPE_PERIODIC_SERVICE => 'Servis Berkala',
        self::TYPE_REPAIR => 'Perbaikan',
        self::TYPE_TIRE_CHANGE => 'Ganti Ban',
        self::TYPE_BRAKE_SERVICE => 'Servis Rem',
        self::TYPE_OTHER => 'Lainnya',
    ];

    protected $fillable = [
        'vehicle_id',
        'workshop_id',
        'technician_id',
        'service_type',
        'service_type_label',
        'service_date',
        'odometer_in',
        'odometer_out',
        'next_service_odometer',
        'next_service_date',
        'cost',
        'notes',
        'parts_replaced',
        'invoice_number',
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
        'odometer_in' => 'integer',
        'odometer_out' => 'integer',
        'next_service_odometer' => 'integer',
    ];

    // =========================================================
    // Relationships
    // =========================================================

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    // =========================================================
    // Scopes
    // =========================================================

    public function scopeByType($query, string $type)
    {
        return $query->where('service_type', $type);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('service_date', '>=', now()->subDays($days));
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Label jenis service yang ramah dibaca.
     */
    public function getServiceTypeLabelReadableAttribute(): string
    {
        if ($this->service_type === self::TYPE_OTHER && $this->service_type_label) {
            return $this->service_type_label;
        }

        return self::SERVICE_TYPES[$this->service_type] ?? $this->service_type;
    }

    /**
     * Format biaya ke Rupiah.
     */
    public function getFormattedCostAttribute(): string
    {
        return 'Rp '.number_format($this->cost, 0, ',', '.');
    }
}
