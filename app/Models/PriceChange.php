<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceChange extends Model
{
    protected $fillable = [
        'workspace_id',
        'competitor_id',
        'snapshot_id',
        'plan_id',
        'change_type',
        'field',
        'old_value',
        'new_value',
        'percentage_change',
        'severity',
        'detected_at',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'percentage_change' => 'float'
    ];

    /**
     * Workspace owning this change log.
     */
    public function workspace()
    {
        // GP - 19-08-2026 code comment - PriceChange workspace relation
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Competitor checked.
     */
    public function competitor()
    {
        // GP - 19-08-2026 code comment - PriceChange competitor relation
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Snapshot capturing this change.
     */
    public function snapshot()
    {
        // GP - 19-08-2026 code comment - PriceChange snapshot relation
        return $this->belongsTo(PricingSnapshot::class, 'snapshot_id');
    }

    /**
     * Plan modified.
     */
    public function plan()
    {
        // GP - 19-08-2026 code comment - PriceChange plan relation
        return $this->belongsTo(PricingPlan::class, 'plan_id');
    }

    /**
     * Team member who reviewed this change.
     */
    public function reviewer()
    {
        // GP - 19-08-2026 code comment - PriceChange reviewer relation
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
