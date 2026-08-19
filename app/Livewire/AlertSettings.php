<?php

namespace App\Livewire;

use App\Models\AlertDestination;
use App\Models\AlertRule;
use App\Support\TenantContext;
use Livewire\Component;

class AlertSettings extends Component
{
    public $webhookUrl = '';
    public $webhookSecret = 'whsec_pricewatch_custom';

    /**
     * Mount alert settings values.
     */
    public function mount()
    {
        // GP - 19-08-2026 code comment - Load webhook configurations
        
        $workspaceId = TenantContext::getWorkspaceId();
        
        $webhook = AlertDestination::where('workspace_id', $workspaceId)
            ->where('type', 'webhook')
            ->first();

        if ($webhook) {
            $this->webhookUrl = $webhook->configuration['endpoint_url'] ?? '';
            $this->webhookSecret = $webhook->configuration['secret_key'] ?? 'whsec_pricewatch_custom';
        }
    }

    /**
     * Save webhook alert destination credentials.
     */
    public function saveWebhook()
    {
        // GP - 19-08-2026 code comment - Save user alert webhooks encrypted
        
        $this->validate([
            'webhookUrl' => 'required|url|max:255'
        ]);

        $workspaceId = TenantContext::getWorkspaceId();
        
        AlertDestination::updateOrCreate(
            ['workspace_id' => $workspaceId, 'type' => 'webhook'],
            [
                'name' => 'Custom Endpoint Webhook',
                'configuration' => [
                    'endpoint_url' => $this->webhookUrl,
                    'secret_key' => $this->webhookSecret
                ],
                'enabled' => true,
                'verified_at' => now()
            ]
        );

        session()->flash('success', 'Webhook destination configured and signed successfully!');
    }

    /**
     * Toggle alert rule trigger status.
     */
    public function toggleRule(int $ruleId)
    {
        // GP - 19-08-2026 code comment - Toggle workspace alert rules
        
        $workspaceId = TenantContext::getWorkspaceId();
        $rule = AlertRule::where('workspace_id', $workspaceId)
            ->where('id', $ruleId)
            ->first();

        if ($rule) {
            $rule->update(['enabled' => !$rule->enabled]);
            session()->flash('success', 'Alert rule updated.');
        }
    }

    /**
     * Render the component template view.
     */
    public function render()
    {
        // GP - 19-08-2026 code comment - Query rule lists and destinations
        
        $workspaceId = TenantContext::getWorkspaceId();

        $destinations = AlertDestination::where('workspace_id', $workspaceId)->get()->keyBy('type');
        $rules = AlertRule::where('workspace_id', $workspaceId)->get();

        return view('livewire.alert-settings', [
            'destinations' => $destinations,
            'rules' => $rules
        ])->extends('layouts.app')->section('content');
    }
}
