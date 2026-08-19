@section('title', 'Competitor Pricing Changes')

<div class="space-y-6" x-data="{ showModal: @entangle('showCompareModal') }">
  <div>
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Pricing Changes</h2>
    <p class="text-sm text-gray-500">Every competitor pricing change detected by PriceWatch.</p>
  </div>

  <!-- Filters -->
  <div class="bg-white p-4 rounded-xl border border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
      <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Change Type</label>
      <select wire:model.live="typeFilter" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-white focus:outline-none">
        <option value="all">All Changes</option>
        <option value="increase">Price Increase</option>
        <option value="decrease">Price Decrease</option>
        <option value="new plan">New Plan</option>
        <option value="removed">Plan Removed</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Severity</label>
      <select wire:model.live="severityFilter" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-white focus:outline-none">
        <option value="all">All Severities</option>
        <option value="high">High Severity</option>
        <option value="medium">Medium Severity</option>
        <option value="low">Low Severity</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Status</label>
      <select wire:model.live="statusFilter" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-white focus:outline-none">
        <option value="all">All Statuses</option>
        <option value="new">New / Unreviewed</option>
        <option value="reviewed">Reviewed</option>
      </select>
    </div>
  </div>

  <!-- Timeline Changes Cards List -->
  <div class="space-y-4">
    @forelse ($changes as $c)
      @php
        $isHigh = $c->severity === 'high';
        $isMed = $c->severity === 'medium';
        $borderClass = $isHigh ? 'border-l-4 border-l-red-500 border-red-100' : ($isMed ? 'border-l-4 border-l-amber-500 border-amber-100' : 'border-l-4 border-l-blue-500 border-blue-100');
        $severityBadge = $isHigh ? 'bg-red-50 text-red-700' : ($isMed ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700');
      @endphp
      
      <div class="bg-white p-6 rounded-xl border {{ $borderClass }} shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row justify-between gap-6 cursor-pointer" wire:click="openComparison({{ $c->id }})">
        <div class="space-y-3 flex-1">
          <div class="flex items-center gap-3">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $severityBadge }}">{{ ucfirst($c->severity) }} Impact</span>
            <span class="text-xs text-gray-400 font-semibold">{{ $c->detected_at->diffForHumans() }}</span>
          </div>
          <div>
            <h4 class="font-bold text-gray-900 text-lg">{{ $c->competitor->name }} &mdash; {{ $c->plan?->name ?? 'Tier' }}</h4>
            <p class="text-sm text-gray-600 mt-1">Plan {{ str_replace('_', ' ', $c->change_type) }} detected on public pricing page.</p>
          </div>
          <div class="flex items-center gap-6 pt-2">
            <div class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
              <span class="text-xs text-gray-400 block">Previous</span>
              <span class="text-sm text-gray-500 line-through font-medium">{{ $c->old_value ?? '—' }}</span>
            </div>
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            <div class="bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100">
              <span class="text-xs text-indigo-400 block">Current</span>
              <span class="text-sm text-indigo-700 font-bold">{{ $c->new_value ?? '—' }}</span>
            </div>
            @if ($c->percentage_change)
              <div class="text-right">
                <span class="text-xs text-gray-400 block">Change %</span>
                <span class="text-sm font-extrabold {{ $c->percentage_change > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                  {{ ($c->percentage_change > 0 ? '+' : '') . $c->percentage_change . '%' }}
                </span>
              </div>
            @endif
          </div>
        </div>
        <div class="flex flex-row md:flex-col justify-end items-end gap-3 self-center">
          @if (!$c->reviewed_at)
            <button wire:click.stop="markReviewed({{ $c->id }})" class="w-full md:w-auto text-xs font-semibold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">Mark Reviewed</button>
          @else
            <span class="text-xs font-medium text-gray-400 bg-gray-100 border border-gray-200 px-3 py-1 rounded-lg">Reviewed</span>
          @endif
          <button wire:click.stop="openComparison({{ $c->id }})" class="w-full md:w-auto text-xs font-semibold px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-lg transition-colors">Compare Details</button>
        </div>
      </div>
    @empty
      <div class="text-center py-12 bg-white rounded-xl border border-gray-100 text-gray-500">
        No pricing changes found matching your filters.
      </div>
    @endforelse
  </div>

  <!-- Detail Compare Modal Overlay -->
  <div x-show="showModal" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 max-w-3xl w-full p-6 relative" @click.away="showModal = false">
      @if ($selectedChange)
        <div class="flex items-center justify-between mb-4">
          <div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $selectedChange->severity === 'high' ? 'bg-red-50 text-red-700 border-red-100' : 'bg-blue-50 text-blue-700 border-blue-100' }} border">{{ strtoupper($selectedChange->severity) }} IMPACT</span>
            <h3 class="text-xl font-bold text-gray-900 mt-2">Pricing change detected &mdash; {{ $selectedChange->competitor->name }}</h3>
            <p class="text-xs text-gray-500">Detected on {{ $selectedChange->detected_at->format('F d, Y \a\t H:i A') }}</p>
          </div>
          <button wire:click="closeComparison" class="text-gray-400 hover:text-gray-600 p-1 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Before / After Panels -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
          <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Before Pricing Change</h4>
            <div class="space-y-4">
              <div>
                <span class="text-xs text-gray-400 block">Plan Tier Name</span>
                <span class="text-sm font-semibold text-gray-900">{{ $selectedChange->plan?->name ?? '—' }}</span>
              </div>
              <div>
                <span class="text-xs text-gray-400 block">Price / Value</span>
                <span class="text-xl font-extrabold text-gray-600 line-through">{{ $selectedChange->old_value ?? '—' }}</span>
              </div>
            </div>
          </div>

          <div class="bg-indigo-50/50 p-5 rounded-xl border border-indigo-100/50">
            <h4 class="text-xs font-semibold text-indigo-500 uppercase tracking-wider mb-3">After Pricing Change</h4>
            <div class="space-y-4">
              <div>
                <span class="text-xs text-gray-400 block">Plan Tier Name</span>
                <span class="text-sm font-semibold text-indigo-700">{{ $selectedChange->plan?->name ?? '—' }}</span>
              </div>
              <div>
                <span class="text-xs text-gray-400 block">Price / Value</span>
                <span class="text-xl font-extrabold text-indigo-700">{{ $selectedChange->new_value ?? '—' }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between border-t border-gray-100 pt-4">
          <a href="{{ $selectedChange->competitor->pricing_url }}" target="_blank" class="text-sm text-indigo-600 hover:underline flex items-center gap-1.5 font-medium">
            <span>Open Source Page</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
          </a>
          <div class="flex gap-2">
            <button wire:click="closeComparison" class="text-xs font-semibold px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-700">Close</button>
            @if (!$selectedChange->reviewed_at)
              <button wire:click="markReviewed({{ $selectedChange->id }})" @click="showModal = false" class="text-xs font-semibold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Mark Reviewed</button>
            @endif
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
