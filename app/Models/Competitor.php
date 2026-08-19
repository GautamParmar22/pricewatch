<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'name',
        'slug',
        'website_url',
        'pricing_url',
        'logo_url',
        'status',
        'check_frequency',
        'last_checked_at',
        'next_check_at',
        'created_by'
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'next_check_at' => 'datetime'
    ];

    /**
     * Get the workspace that owns the competitor.
     */
    public function workspace()
    {
        // GP - 19-08-2026 code comment - Competitor workspace relation
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the snapshots for the competitor.
     */
    public function snapshots()
    {
        // GP - 19-08-2026 code comment - Competitor snapshots relation
        return $this->hasMany(PricingSnapshot::class);
    }

    /**
     * Get the plans for the competitor.
     */
    public function plans()
    {
        // GP - 19-08-2026 code comment - Competitor plans relation
        return $this->hasMany(PricingPlan::class);
    }

    /**
     * Get the changes detected for the competitor.
     */
    public function changes()
    {
        // GP - 19-08-2026 code comment - Competitor changes relation
        return $this->hasMany(PriceChange::class);
    }

    /**
     * Get the user who created the competitor tracking.
     */
    public function creator()
    {
        // GP - 19-08-2026 code comment - Competitor creator relation
        return $this->belongsTo(User::class, 'created_by');
    }
}
