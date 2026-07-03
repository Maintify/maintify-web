<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /**
     * Append-only: hanya menggunakan created_at, tanpa updated_at.
     */
    public $timestamps = false;

    protected $table = 'audit_logs';

    protected $fillable = [
        'actor_user_id',
        'action',
        'entity_type',
        'entity_id',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relationships
    // =========================================================

    /**
     * User yang melakukan aksi.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    // =========================================================
    // Static Helper
    // =========================================================

    /**
     * Catat audit log baru (append-only).
     */
    public static function record(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        array $metadata = []
    ): self {
        return self::create([
            'actor_user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata ?: null,
            'ip_address' => request()->ip(),
        ]);
    }
}
