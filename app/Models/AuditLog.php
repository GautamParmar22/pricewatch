<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false; // standard created_at useCurrent

    protected $fillable = [
        'workspace_id',
        'user_id',
        'action',
        'resource',
        'resource_id',
        'metadata',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime'
    ];

    /**
     * Workspace where this audit event occurred.
     */
    public function workspace()
    {
        // GP - 19-08-2026 code comment - AuditLog workspace relation
        return $this->belongsTo(Workspace::class);
    }

    /**
     * User who executed this audit event.
     */
    public function user()
    {
        // GP - 19-08-2026 code comment - AuditLog user relation
        return $this->belongsTo(User::class);
    }
}
