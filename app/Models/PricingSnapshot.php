<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingSnapshot extends Model
{
    public $timestamps = false; // captured_at is standard timestamp

    protected $fillable = [
        'competitor_id',
        'workspace_id',
        'content_hash',
        'raw_content',
        'normalized_data',
        'http_status',
        'captured_at'
    ];

    protected $casts = [
        'normalized_data' => 'array',
        'captured_at' => 'datetime'
    ];

    /**
     * Get the competitor that owns the snapshot.
     */
    public function competitor()
    {
        // GP - 19-08-2026 code comment - PricingSnapshot competitor relation
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Get the workspace that owns the snapshot.
     */
    public function workspace()
    {
        // GP - 19-08-2026 code comment - PricingSnapshot workspace relation
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Plans associated with this snapshot.
     */
    public function plans()
    {
        // GP - 19-08-2026 code comment - PricingSnapshot plans relation
        return $this->hasMany(PricingPlan::class, 'snapshot_id');
    }

    /**
     * Changes triggered during this snapshot.
     */
    public function changes()
    {
        // GP - 19-08-2026 code comment - PricingSnapshot changes relation
        return $this->hasMany(PriceChange::class, 'snapshot_id');
    }
}
