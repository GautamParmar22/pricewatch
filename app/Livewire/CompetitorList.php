<?php

namespace App\Livewire;

use App\Models\Competitor;
use App\Support\TenantContext;
use Livewire\Component;

class CompetitorList extends Component
{
    public $search = '';
    public $filter = 'all';
    public $showAddModal = false;

    // Listeners to refresh list when competitors are added
    protected $listeners = ['competitorAdded' => 'refreshList'];

    /**
     * Mount component parameters.
     */
    public function mount()
    {
        // GP - 19-08-2026 code comment - Check URL request parameter to show Add Competitor modal
        if (request()->query('add')) {
            $this->showAddModal = true;
        }
    }

    /**
     * Refresh the list event handler.
     */
    public function refreshList()
    {
        // GP - 19-08-2026 code comment - Refresh lists upon additions
    }

    /**
     * Toggle tracking state (Pause/Resume monitoring schedule).
     */
    public function toggleStatus(int $competitorId)
    {
        // GP - 19-08-2026 code comment - Pause or resume scraping schedules
        
        $workspaceId = TenantContext::getWorkspaceId();
        $competitor = Competitor::where('workspace_id', $workspaceId)
            ->where('id', $competitorId)
            ->first();

        if ($competitor) {
            $newStatus = $competitor->status === 'active' ? 'paused' : 'active';
            $competitor->update(['status' => $newStatus]);
            
            $msg = $newStatus === 'active' ? 'Resumed monitoring successfully!' : 'Monitoring paused.';
            session()->flash('success', "{$competitor->name}: {$msg}");
        }
    }

    /**
     * Render competitors list.
     */
    public function render()
    {
        // GP - 19-08-2026 code comment - Query competitors list applying filters
        
        $workspaceId = TenantContext::getWorkspaceId();
        $query = Competitor::where('workspace_id', $workspaceId);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('website_url', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->filter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->filter === 'paused') {
            $query->where('status', 'paused');
        }

        $competitors = $query->latest()->get();

        return view('livewire.competitor-list', [
            'competitors' => $competitors
        ])->extends('layouts.app')->section('content');
    }
}
