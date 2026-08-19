@section('title', 'Monitor Competitors')

<div class="space-y-6" x-data="{ showModal: @entangle('showAddModal') }">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Competitors</h2>
      <p class="text-sm text-gray-500">Monitor and compare the companies that matter to your pricing strategy.</p>
    </div>
    <button @click="showModal = true" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 rounded-lg shadow-sm hover-lift flex items-center gap-1.5">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
      Add Competitor
    </button>
  </div>

  <!-- Search and Tab Filters -->
  <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="w-full md:max-w-xs relative">
      <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      </span>
      <input wire:model.live="search" type="text" placeholder="Search competitors..." class="w-full pl-9 pr-4 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <!-- Tab selections -->
    <div class="flex border border-gray-200 p-0.5 rounded-lg text-xs font-bold text-gray-500">
      <button wire:click="$set('filter', 'all')" class="px-4 py-1.5 rounded-md transition-colors {{ $filter === 'all' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : '' }}">All</button>
      <button wire:click="$set('filter', 'active')" class="px-4 py-1.5 rounded-md transition-colors {{ $filter === 'active' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : '' }}">Active</button>
      <button wire:click="$set('filter', 'paused')" class="px-4 py-1.5 rounded-md transition-colors {{ $filter === 'paused' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : '' }}">Paused</button>
    </div>
  </div>

  <div class="bg-white border border-gray-100 rounded-xl shadow-xs overflow-hidden">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-400 text-xs font-bold uppercase">
          <th class="px-6 py-3">Competitor</th>
          <th class="px-6 py-3">Pricing Page</th>
          <th class="px-6 py-3">Last Checked</th>
          <th class="px-6 py-3">Next Scheduled</th>
          <th class="px-6 py-3">Frequency</th>
          <th class="px-6 py-3">Status</th>
          <th class="px-6 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50 text-xs">
        @forelse ($competitors as $c)
          <tr class="hover:bg-gray-50/70 transition-colors border-b border-gray-100">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='{{ route('competitors.show', $c->id) }}'">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center">{{ substr($c->name, 0, 1) }}</span>
                <div>
                  <div class="text-sm font-semibold text-gray-900">{{ $c->name }}</div>
                  <div class="text-xs text-gray-500">{{ parse_url($c->website_url, PHP_URL_HOST) }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <a href="{{ $c->pricing_url }}" target="_blank" class="text-indigo-600 hover:underline flex items-center gap-1">
                pricing_page
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
              </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $c->last_checked_at?->diffForHumans() ?? 'Never' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $c->next_check_at?->diffForHumans() ?? 'As soon as scheduler runs' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-semibold">{{ $c->check_frequency }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $c->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }} border">
                {{ ucfirst($c->status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
              <div class="flex items-center justify-end gap-2">
                <button wire:click="toggleStatus({{ $c->id }})" class="text-[10px] font-bold px-2.5 py-1 rounded border border-gray-200 hover:bg-gray-50 text-gray-600">
                  {{ $c->status === 'active' ? 'Pause' : 'Resume' }}
                </button>
                <a href="{{ route('competitors.show', $c->id) }}" class="text-[10px] font-bold px-2.5 py-1 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded hover:bg-indigo-100">
                  View
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
              <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              No competitors monitored yet.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Embedded Livewire AddCompetitorModal -->
  <div x-show="showModal" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
    <livewire:add-competitor-modal />
  </div>
</div>
