<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

class Workspace extends Model
{
    use HasFactory, Billable;

    protected $fillable = [
        'name',
        'slug',
        'owner_id',
        'timezone'
    ];

    /**
     * Get the owner of the workspace.
     */
    public function owner()
    {
        // GP - 19-08-2026 code comment - Workspace owner relation
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the users belonging to the workspace.
     */
    public function users()
    {
        // GP - 19-08-2026 code comment - Workspace users belongsToMany relation
        return $this->belongsToMany(User::class, 'workspace_user')->withPivot('role')->withTimestamps();
    }

    /**
     * Competitors monitored by the workspace.
     */
    public function competitors()
    {
        // GP - 19-08-2026 code comment - Workspace competitors hasMany relation
        return $this->hasMany(Competitor::class);
    }

    /**
     * Snapshots captured for the workspace.
     */
    public function snapshots()
    {
        // GP - 19-08-2026 code comment - Workspace snapshots hasMany relation
        return $this->hasMany(PricingSnapshot::class);
    }

    /**
     * Changes detected for the workspace.
     */
    public function changes()
    {
        // GP - 19-08-2026 code comment - Workspace changes hasMany relation
        return $this->hasMany(PriceChange::class);
    }

    /**
     * Alert rules configured for the workspace.
     */
    public function alertRules()
    {
        // GP - 19-08-2026 code comment - Workspace alertRules hasMany relation
        return $this->hasMany(AlertRule::class);
    }

    /**
     * Alert destinations configured for the workspace.
     */
    public function alertDestinations()
    {
        // GP - 19-08-2026 code comment - Workspace alertDestinations hasMany relation
        return $this->hasMany(AlertDestination::class);
    }

    /**
     * Audit logs for this workspace.
     */
    public function auditLogs()
    {
        // GP - 19-08-2026 code comment - Workspace auditLogs hasMany relation
        return $this->hasMany(AuditLog::class);
    }
}
