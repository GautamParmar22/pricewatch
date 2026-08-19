<?php

namespace Tests\Feature;

use App\Models\Competitor;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class WorkspaceTenantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test workspace cross-tenant authentication policy isolation.
     */
    public function test_tenant_isolation_policies(): void
    {
        // GP - 19-08-2026 code comment - Test cross-tenant model policies security
        
        // 1. Create Tenant A
        $userA = User::create([
            'name' => 'User A',
            'email' => 'usera@company.com',
            'password' => bcrypt('password123')
        ]);
        $workspaceA = Workspace::create([
            'name' => 'Workspace A',
            'slug' => 'workspace-a',
            'owner_id' => $userA->id
        ]);
        $workspaceA->users()->attach($userA->id, ['role' => 'owner']);

        // 2. Create Tenant B
        $userB = User::create([
            'name' => 'User B',
            'email' => 'userb@company.com',
            'password' => bcrypt('password123')
        ]);
        $workspaceB = Workspace::create([
            'name' => 'Workspace B',
            'slug' => 'workspace-b',
            'owner_id' => $userB->id
        ]);
        $workspaceB->users()->attach($userB->id, ['role' => 'owner']);

        // Create competitor in Workspace A
        $competitorA = Competitor::create([
            'workspace_id' => $workspaceA->id,
            'name' => 'Stripe',
            'slug' => 'stripe',
            'website_url' => 'https://stripe.com',
            'pricing_url' => 'https://stripe.com/pricing',
            'status' => 'active',
            'check_frequency' => 'daily'
        ]);

        // 3. Test Policy checking
        
        // User A should be authorized to view/delete competitor A
        $this->assertTrue(Gate::forUser($userA)->allows('view', $competitorA));
        $this->assertTrue(Gate::forUser($userA)->allows('delete', $competitorA));

        // User B must be BLOCKED from viewing/deleting competitor A
        $this->assertFalse(Gate::forUser($userB)->allows('view', $competitorA));
        $this->assertFalse(Gate::forUser($userB)->allows('delete', $competitorA));
    }
}
