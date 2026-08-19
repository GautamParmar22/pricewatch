<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    public $timestamps = false; // created_at standard timestamp

    protected $fillable = [
        'competitor_id',
        'snapshot_id',
        'name',
        'slug',
        'monthly_price',
        'annual_price',
        'currency',
        'billing_period',
        'description',
        'user_limit',
        'features'
    ];

    protected $casts = [
        'features' => 'array',
        'monthly_price' => 'float',
        'annual_price' => 'float',
        'user_limit' => 'integer'
    ];

    /**
     * Competitor owning this plan.
     */
    public function competitor()
    {
        // GP - 19-08-2026 code comment - PricingPlan competitor relation
        return $this->belongsTo(Competitor::class);
    }

    /**
     * Snapshot this plan was captured in.
     */
    public function snapshot()
    {
        // GP - 19-08-2026 code comment - PricingPlan snapshot relation
        return $this->belongsTo(PricingSnapshot::class, 'snapshot_id');
    }
}
