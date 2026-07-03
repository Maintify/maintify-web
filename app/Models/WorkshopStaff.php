<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopStaff extends Model
{
    use HasFactory;

    protected $table = 'workshop_staff';

    /**
     * Position constants.
     */
    const POSITION_MECHANIC = 'mechanic';

    const POSITION_ADMIN = 'admin';

    protected $fillable = [
        'workshop_id',
        'user_id',
        'position',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    // =========================================================
    // Relationships
    // =========================================================

    /**
     * Get the workshop that employs this staff member.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    /**
     * Get the user account for this staff member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
