<?php

namespace App\Livewire;

use App\Models\PriceChange;
use App\Support\TenantContext;
use Livewire\Component;

class ChangesLog extends Component
{
    public $typeFilter = 'all';
    public $severityFilter = 'all';
    public $statusFilter = 'all';
    public $selectedChangeId = null;
    public $showCompareModal = false;

    /**
     * Mark a price change record as reviewed by user.
     */
    public function markReviewed(int $changeId)
    {
        // GP - 19-08-2026 code comment - Mark price changes as reviewed
        
        $workspaceId = TenantContext::getWorkspaceId();
        $change = PriceChange::where('workspace_id', $workspaceId)
            ->where('id', $changeId)
            ->first();

        if ($change) {
            $change->update([
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id()
            ]);
            session()->flash('success', 'Pricing change marked as reviewed.');
        }
    }

    /**
     * Open details side-by-side comparison modal.
     */
    public function openComparison(int $changeId)
    {
        // GP - 19-08-2026 code comment - Open compare modal
        $this->selectedChangeId = $changeId;
        $this->showCompareModal = true;
    }

    /**
     * Close modal.
     */
    public function closeComparison()
    {
        // GP - 19-08-2026 code comment - Close compare modal
        $this->showCompareModal = false;
        $this->selectedChangeId = null;
    }

    /**
     * Render pricing changes log list.
     */
    public function render()
    {
        // GP - 19-08-2026 code comment - Query changes list applying filters
        
        $workspaceId = TenantContext::getWorkspaceId();
        $query = PriceChange::where('workspace_id', $workspaceId)
            ->with(['competitor', 'plan']);

        if ($this->typeFilter !== 'all') {
            if ($this->typeFilter === 'increase') {
                $query->where('change_type', 'price_increased');
            } elseif ($this->typeFilter === 'decrease') {
                $query->where('change_type', 'price_decreased');
            } elseif ($this->typeFilter === 'new plan') {
                $query->where('change_type', 'plan_added');
            } elseif ($this->typeFilter === 'removed') {
                $query->where('change_type', 'plan_removed');
            }
        }

        if ($this->severityFilter !== 'all') {
            $query->where('severity', $this->severityFilter);
        }

        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'new') {
                $query->whereNull('reviewed_at');
            } elseif ($this->statusFilter === 'reviewed') {
                $query->whereNotNull('reviewed_at');
            }
        }

        $changes = $query->latest('detected_at')->get();
        $selectedChange = $this->selectedChangeId ? PriceChange::with(['competitor', 'plan'])->find($this->selectedChangeId) : null;

        return view('livewire.changes-log', [
            'changes' => $changes,
            'selectedChange' => $selectedChange
        ])->extends('layouts.app')->section('content');
    }
}
