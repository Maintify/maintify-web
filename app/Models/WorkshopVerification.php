<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopVerification extends Model
{
    use HasFactory;

    protected $table = 'workshop_verifications';

    /**
     * Status constants.
     */
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_REVISION_NEEDED = 'revision_needed';

    protected $fillable = [
        'workshop_id',
        'reviewed_by',
        'status',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // =========================================================
    // Relationships
    // =========================================================

    /**
     * Get the workshop being verified.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Get the super admin who reviewed the workshop.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
