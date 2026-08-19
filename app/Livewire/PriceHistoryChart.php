<?php

namespace App\Livewire;

use App\Models\Competitor;
use App\Models\PriceChange;
use App\Support\TenantContext;
use Livewire\Component;

class PriceHistoryChart extends Component
{
    public $competitorId = 'all';

    /**
     * Render price history analytics view.
     */
    public function render()
    {
        // GP - 19-08-2026 code comment - Gather price history data and build template view
        
        $workspaceId = TenantContext::getWorkspaceId();
        $competitors = Competitor::where('workspace_id', $workspaceId)->get();

        $query = PriceChange::where('workspace_id', $workspaceId)
            ->with(['competitor', 'plan']);

        if ($this->competitorId !== 'all') {
            $query->where('competitor_id', $this->competitorId);
        }

        $historyLog = $query->latest('detected_at')->get();

        return view('livewire.price-history-chart', [
            'competitors' => $competitors,
            'historyLog' => $historyLog
        ])->extends('layouts.app')->section('content');
    }
}
