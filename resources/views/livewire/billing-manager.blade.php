@section('title', 'Subscription & Billing')

<div class="space-y-6">
  <div>
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Billing</h2>
    <p class="text-sm text-gray-500">Review your current plan subscriptions and active limits.</p>
  </div>

  <!-- Subscription details -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-6">
      <h3 class="font-extrabold text-gray-900 text-lg border-b border-gray-50 pb-3">Current Plan</h3>
      <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-200/50">
        <div>
          <span class="text-xl font-extrabold text-gray-900">{{ $planName }} Plan</span>
          <span class="text-xs text-gray-500 block mt-1">Next invoice scheduled for {{ now()->addMonth()->format('F d, Y') }}</span>
        </div>
        <div class="text-right">
          <span class="text-2xl font-extrabold text-indigo-600">${{ $price }}/month</span>
        </div>
      </div>

      <!-- Usage Meters -->
      <div class="space-y-4 border-t border-gray-50 pt-4">
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Monthly Quotas</h4>
        
        <!-- Competitors meter -->
        <div>
          <div class="flex justify-between text-xs font-semibold mb-1">
            <span class="text-gray-600">Competitors Monitored</span>
            <span class="text-gray-900">{{ $competitorsCount }} / {{ $competitorsLimit }}</span>
          </div>
          <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
            <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" style="width: {{ $competitorsPercent }}%"></div>
          </div>
        </div>

        <!-- Checks meter -->
        <div>
          <div class="flex justify-between text-xs font-semibold mb-1">
            <span class="text-gray-600">Monitoring checks</span>
            <span class="text-gray-900">{{ number_format($checksCount) }} / {{ number_format($checksLimit) }}</span>
          </div>
          <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
            <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" style="width: {{ $checksPercent }}%"></div>
          </div>
        </div>

        <!-- Team members meter -->
        <div>
          <div class="flex justify-between text-xs font-semibold mb-1">
            <span class="text-gray-600">Team seats</span>
            <span class="text-gray-900">{{ $usersCount }} / {{ $usersLimit }}</span>
          </div>
          <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
            <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" style="width: {{ $usersPercent }}%"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="md:col-span-1 bg-white p-6 rounded-xl border border-gray-100 shadow-xs flex flex-col justify-between">
      <div>
        <h3 class="font-extrabold text-gray-900 text-lg border-b border-gray-50 pb-3">Payment Methods</h3>
        <div class="mt-4 flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
          <span class="w-10 h-6 bg-indigo-600 rounded text-white flex items-center justify-center text-[10px] font-bold">VISA</span>
          <div>
            <span class="text-sm font-semibold text-gray-950">Visa ending in 4242</span>
            <span class="text-xs text-gray-400 block">Expires 12/2029</span>
          </div>
        </div>
      </div>
      <button class="mt-6 w-full text-xs font-semibold px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700 transition-colors" onclick="alert('Redirecting to Stripe Billing Portal...')">Manage Payment Methods</button>
    </div>
  </div>

  <!-- Plans Switcher grid -->
  <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-6">
    <h3 class="font-extrabold text-gray-900 text-lg border-b border-gray-50 pb-3">Switch Subscription Plan</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Starter -->
      <div class="border border-gray-100 p-5 rounded-xl flex flex-col justify-between {{ $planName === 'Starter' ? 'border-2 border-indigo-600' : '' }}">
        <div>
          <h4 class="font-bold text-gray-900 text-sm">Starter</h4>
          <div class="mt-2 text-xl font-extrabold text-indigo-600">$19/mo</div>
          <p class="text-xs text-gray-400 mt-2">Up to 10 competitors, Daily monitoring intervals.</p>
        </div>
        @if ($planName === 'Starter')
          <button disabled class="mt-6 w-full py-2 bg-gray-100 text-gray-400 text-xs font-bold rounded-lg cursor-not-allowed">Current Plan</button>
        @else
          <button wire:click="selectPlan('Starter')" class="mt-6 w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition-colors">Select Plan</button>
        @endif
      </div>

      <!-- Growth -->
      <div class="border border-gray-100 p-5 rounded-xl flex flex-col justify-between {{ $planName === 'Growth' ? 'border-2 border-indigo-600' : '' }}">
        <div>
          <h4 class="font-bold text-gray-900 text-sm">Growth</h4>
          <div class="mt-2 text-xl font-extrabold text-indigo-600">$49/mo</div>
          <p class="text-xs text-gray-400 mt-2">Up to 25 competitors, Hourly checks intervals, Slack integration.</p>
        </div>
        @if ($planName === 'Growth')
          <button disabled class="mt-6 w-full py-2 bg-gray-100 text-gray-400 text-xs font-bold rounded-lg cursor-not-allowed">Current Plan</button>
        @else
          <button wire:click="selectPlan('Growth')" class="mt-6 w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition-colors">Select Plan</button>
        @endif
      </div>

      <!-- Pro -->
      <div class="border border-gray-100 p-5 rounded-xl flex flex-col justify-between {{ $planName === 'Pro' ? 'border-2 border-indigo-600' : '' }}">
        <div>
          <h4 class="font-bold text-gray-900 text-sm">Pro</h4>
          <div class="mt-2 text-xl font-extrabold text-indigo-600">$99/mo</div>
          <p class="text-xs text-gray-400 mt-2">Up to 100 competitors, Instant webhooks, Developer API.</p>
        </div>
        @if ($planName === 'Pro')
          <button disabled class="mt-6 w-full py-2 bg-gray-100 text-gray-400 text-xs font-bold rounded-lg cursor-not-allowed">Current Plan</button>
        @else
          <button wire:click="selectPlan('Pro')" class="mt-6 w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors shadow-xs">Upgrade Plan</button>
        @endif
      </div>
    </div>
  </div>
</div>
