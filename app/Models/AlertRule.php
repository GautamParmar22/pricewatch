<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertRule extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'enabled',
        'change_types',
        'minimum_percentage_change',
        'competitor_ids',
        'channels'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'change_types' => 'array',
        'competitor_ids' => 'array',
        'channels' => 'array',
        'minimum_percentage_change' => 'float'
    ];

    /**
     * Workspace owning this rule.
     */
    public function workspace()
    {
        // GP - 19-08-2026 code comment - AlertRule workspace relation
        return $this->belongsTo(Workspace::class);
    }
}
