<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertDestination extends Model
{
    protected $fillable = [
        'workspace_id',
        'type',
        'name',
        'configuration',
        'enabled',
        'verified_at'
    ];

    protected $casts = [
        'configuration' => 'encrypted:array',
        'enabled' => 'boolean',
        'verified_at' => 'datetime'
    ];

    /**
     * Workspace owning this destination channel.
     */
    public function workspace()
    {
        // GP - 19-08-2026 code comment - AlertDestination workspace relation
        return $this->belongsTo(Workspace::class);
    }
}
