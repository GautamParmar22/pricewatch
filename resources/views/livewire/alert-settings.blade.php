@section('title', 'Alert Channels Configurations')

<div class="space-y-6">
  <div>
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Alerts</h2>
    <p class="text-sm text-gray-500">Choose how PriceWatch notifies you about important changes.</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Email -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between min-h-[180px]">
      <div>
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-bold text-gray-950 text-base">Email Alerts</h4>
          <span class="text-emerald-600 text-xs font-semibold">Connected</span>
        </div>
        <p class="text-xs text-gray-500 mb-4">Sends daily summary and instant critical price change alerts to your inbox.</p>
        <div class="text-sm text-gray-900 font-bold">{{ Auth::user()->email }}</div>
      </div>
      <button class="mt-6 w-full text-xs font-semibold px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors cursor-not-allowed" disabled>Configured via Account</button>
    </div>

    <!-- Slack -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between min-h-[180px]">
      <div>
        <div class="flex items-center justify-between mb-4">
          <h4 class="font-bold text-gray-950 text-base">Slack Alerts</h4>
          <span class="text-emerald-600 text-xs font-semibold">Connected</span>
        </div>
        <p class="text-xs text-gray-500 mb-4">Broadcasts automated notifications to Slack channels on change events.</p>
        <div class="text-sm text-gray-900 font-bold">#competitive-intelligence</div>
      </div>
      <button class="mt-6 w-full text-xs font-semibold px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors cursor-not-allowed" disabled>Configured via Auth</button>
    </div>

    <!-- Webhook -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between min-h-[220px] md:col-span-1">
      <form wire:submit.prevent="saveWebhook" class="space-y-3">
        <div class="flex items-center justify-between">
          <h4 class="font-bold text-gray-950 text-base">Webhook Alerts</h4>
          <span class="{{ isset($destinations['webhook']) ? 'text-emerald-600' : 'text-gray-400' }} text-xs font-semibold">
            {{ isset($destinations['webhook']) ? 'Connected' : 'Not configured' }}
          </span>
        </div>
        
        <div>
          <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Endpoint URL</label>
          <input wire:model="webhookUrl" type="url" placeholder="https://api.yourdomain.com/webhooks" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white">
          @error('webhookUrl') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Secret Key (HMAC signing)</label>
          <input wire:model="webhookSecret" type="text" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-50 text-gray-500 focus:outline-none" readonly>
        </div>

        <button type="submit" class="w-full text-xs font-semibold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
          Configure Webhook
        </button>
      </form>
    </div>
  </div>

  <!-- Alert rules -->
  <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
    <h3 class="font-extrabold text-gray-950 text-lg border-b border-gray-50 pb-3">Alert Trigger Rules</h3>
    <div class="space-y-3">
      @forelse ($rules as $rule)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200/50 hover:bg-gray-50/80 transition-colors">
          <span class="text-sm font-medium text-gray-700">{{ $rule->name }}</span>
          <button wire:click="toggleRule({{ $rule->id }})" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $rule->enabled ? 'bg-indigo-600' : 'bg-gray-200' }}">
            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $rule->enabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
          </button>
        </div>
      @empty
        <div class="text-center py-4 text-gray-500">No alert rules configured.</div>
      @endforelse
    </div>
  </div>
</div>
