<?php

namespace App\Livewire;

use App\Jobs\CheckCompetitorPricing;
use App\Models\Competitor;
use App\Services\Billing\UsageLimitService;
use App\Support\TenantContext;
use Illuminate\Support\Str;
use Livewire\Component;

class AddCompetitorModal extends Component
{
    public $name = '';
    public $websiteUrl = '';
    public $pricingUrl = '';
    public $frequency = 'daily';
    public $channels = ['email'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'websiteUrl' => 'required|url|max:255',
        'pricingUrl' => 'required|url|max:255',
        'frequency' => 'required|string|in:hourly,every_6_hours,daily,weekly',
    ];

    /**
     * Submit action to insert competitor and dispatch baseline crawl jobs.
     */
    public function save(UsageLimitService $limitService)
    {
        // GP - 19-08-2026 code comment - Validate, resolve limits and insert competitor
        
        $this->validate();

        $workspace = TenantContext::getWorkspace();

        // 1. Quota check
        if (!$limitService->canAddCompetitor($workspace)) {
            $this->addError('name', 'Workspace competitor limit reached. Please upgrade your billing plan.');
            return;
        }

        // 2. Duplicate checking
        $exists = Competitor::where('workspace_id', $workspace->id)
            ->where('pricing_url', $this->pricingUrl)
            ->exists();

        if ($exists) {
            $this->addError('pricingUrl', 'This pricing URL is already monitored in your workspace.');
            return;
        }

        // 3. Save competitor
        $competitor = Competitor::create([
            'workspace_id' => $workspace->id,
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'website_url' => $this->websiteUrl,
            'pricing_url' => $this->pricingUrl,
            'status' => 'active',
            'check_frequency' => $this->frequency,
            'created_by' => auth()->id()
        ]);

        // 4. Dispatch baseline checking job
        CheckCompetitorPricing::dispatch($competitor);

        session()->flash('success', "Added {$this->name} successfully. Scraper baseline job dispatched.");
        
        $this->reset(['name', 'websiteUrl', 'pricingUrl', 'frequency']);
        
        // Notify parent component to close modal and refresh list
        $this->dispatch('competitorAdded');
        
        // Redirect to reload lists
        return redirect()->route('competitors.index');
    }

    /**
     * Render Livewire template view.
     */
    public function render()
    {
        // GP - 19-08-2026 code comment - Render add modal component view
        return view('livewire.add-competitor-modal');
    }
}
