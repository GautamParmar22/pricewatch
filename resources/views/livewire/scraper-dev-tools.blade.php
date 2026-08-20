<div class="p-6 space-y-6">
  
  <!-- Header Title -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-slate-900">Scraper Developer Tools</h1>
      <p class="text-sm text-gray-500 mt-1">Directly trigger, monitor, and debug the price monitoring scraping engine.</p>
    </div>
    
    <!-- Global Actions -->
    <div class="flex flex-wrap gap-3">
      <button 
        wire:click="scrapeAllSync" 
        wire:loading.attr="disabled"
        id="btn-scrape-all-sync"
        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition-colors duration-150 disabled:opacity-50">
        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17"></path></svg>
        <span>Scrape All Active (Sync)</span>
      </button>
      
      <button 
        wire:click="queueAll" 
        wire:loading.attr="disabled"
        id="btn-queue-all"
        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg text-sm font-semibold transition-colors duration-150 disabled:opacity-50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        <span>Queue All Active</span>
      </button>
    </div>
  </div>

  @if (session()->has('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 text-sm rounded-lg flex items-center gap-2 shadow-sm">
      <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  <!-- Grid for Stats & Console -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column: Metrics & Console Output -->
    <div class="lg:col-span-2 space-y-6 flex flex-col justify-between">
      
      <!-- Metrics Card Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Active Competitors -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <div>
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Trackers</div>
            <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $competitors->where('status', 'active')->count() }}</div>
          </div>
        </div>

        <!-- Pending Queue Jobs -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 border border-amber-100 text-amber-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <div>
              <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending Jobs</div>
              <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $pendingJobsCount }}</div>
            </div>
          </div>
          
          <!-- Run Worker Button -->
          @if ($pendingJobsCount > 0)
            <button 
              wire:click="processQueue" 
              wire:loading.attr="disabled"
              id="btn-process-queue"
              title="Process Next Job"
              class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-md text-xs font-bold transition-all flex items-center gap-1 shadow-sm disabled:opacity-50">
              <span wire:loading.remove wire:target="processQueue">Run Job</span>
              <span wire:loading wire:target="processQueue" class="inline-block animate-spin w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full"></span>
            </button>
          @endif
        </div>

        <!-- System Status -->
        <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          </div>
          <div>
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Engine Safety</div>
            <div class="text-sm font-extrabold text-emerald-700 mt-1 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              <span>SSRF Filter Active</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Developer Console Output Box -->
      <div class="bg-slate-900 text-slate-100 rounded-xl overflow-hidden shadow-md flex flex-col flex-1 mt-6">
        <div class="bg-slate-800 px-4 py-2 border-b border-slate-700/80 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
            <span class="w-3 h-3 rounded-full bg-green-500"></span>
            <span class="text-xs font-mono font-semibold text-slate-400 ml-2">dev-scraper-shell ~ logs</span>
          </div>
          
          <button 
            wire:click="clearConsole" 
            id="btn-clear-console"
            class="text-[10px] uppercase font-bold text-slate-400 hover:text-slate-100 px-2 py-0.5 border border-slate-700 rounded transition-colors">
            Clear Console
          </button>
        </div>
        
        <div class="p-4 font-mono text-xs overflow-y-auto space-y-1.5 h-64 max-h-64 scrollbar-thin scrollbar-thumb-slate-800 bg-slate-950/80">
          @foreach ($consoleOutput as $line)
            <div class="leading-relaxed {{ str_contains($line, 'ERROR') || str_contains($line, 'EXCEPTION') ? 'text-red-400' : (str_contains($line, 'SUCCESS') ? 'text-emerald-400' : (str_contains($line, 'DISPATCHED') ? 'text-cyan-400' : 'text-slate-300')) }}">
              {{ $line }}
            </div>
          @endforeach
          
          <div wire:loading class="text-indigo-400 animate-pulse">
            [Executing background scraping query, please wait...]
          </div>
        </div>
      </div>

    </div>

    <!-- Right Column: Sidebar Stats (Snapshots & Price Changes) -->
    <div class="space-y-6">
      
      <!-- Recent Snapshots List -->
      <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Latest Snapshots</h2>
        <div class="divide-y divide-gray-100">
          @forelse ($latestSnapshots as $snap)
            <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
              <div class="min-w-0">
                <span class="font-bold text-slate-800 truncate block">{{ $snap->competitor->name }}</span>
                <span class="font-mono text-[10px] text-gray-400 block mt-0.5">Hash: {{ substr($snap->content_hash, 0, 8) }}...</span>
              </div>
              <div class="text-right shrink-0">
                <span class="px-1.5 py-0.5 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded font-bold">HTTP {{ $snap->http_status }}</span>
                <span class="text-[10px] text-gray-400 block mt-1">{{ $snap->captured_at->diffForHumans() }}</span>
              </div>
            </div>
          @empty
            <div class="py-4 text-center text-gray-400 text-xs">No snapshots captured yet.</div>
          @endforelse
        </div>
      </div>

      <!-- Recent Changes List -->
      <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm space-y-4">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Recent Price Changes</h2>
        <div class="divide-y divide-gray-100">
          @forelse ($latestChanges as $change)
            <div class="py-2.5 space-y-1 text-xs">
              <div class="flex items-center justify-between">
                <span class="font-bold text-slate-800">{{ $change->competitor->name }}</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $change->severity === 'high' ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-indigo-50 text-indigo-700 border border-indigo-100' }}">
                  {{ strtoupper($change->severity) }}
                </span>
              </div>
              <div class="flex items-center justify-between text-gray-500">
                <span>{{ $change->plan?->name ?? 'Global' }} : {{ ucwords(str_replace('_', ' ', $change->change_type)) }}</span>
                <span class="font-bold text-slate-700">{{ $change->old_value }} &rarr; {{ $change->new_value }}</span>
              </div>
            </div>
          @empty
            <div class="py-4 text-center text-gray-400 text-xs">No price shifts captured yet.</div>
          @endforelse
        </div>
      </div>

    </div>

  </div>

  <!-- Bottom Panel: Competitor List Table -->
  <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
      <h2 class="text-sm font-bold text-slate-800">Monitored Competitors (Workspace Scope)</h2>
      <span class="text-xs font-medium text-gray-400">Total count: {{ $competitors->count() }}</span>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase font-semibold">
            <th class="px-6 py-3.5">Name</th>
            <th class="px-6 py-3.5">Target Pricing URL</th>
            <th class="px-6 py-3.5">Frequency</th>
            <th class="px-6 py-3.5">Last Checked</th>
            <th class="px-6 py-3.5">Next Check Due</th>
            <th class="px-6 py-3.5 text-center">Status</th>
            <th class="px-6 py-3.5 text-right">Developer Tools Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-slate-700">
          @forelse ($competitors as $comp)
            <tr class="hover:bg-slate-50/70 transition-colors">
              <td class="px-6 py-4 font-bold text-slate-900">{{ $comp->name }}</td>
              <td class="px-6 py-4">
                <a href="{{ $comp->pricing_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 hover:underline flex items-center gap-1">
                  <span>{{ Str::limit($comp->pricing_url, 40) }}</span>
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
              </td>
              <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $comp->check_frequency) }}</td>
              <td class="px-6 py-4 text-gray-500">
                {{ $comp->last_checked_at ? $comp->last_checked_at->format('M d, H:i:s') : 'Never' }}
              </td>
              <td class="px-6 py-4 text-gray-500">
                {{ $comp->next_check_at ? $comp->next_check_at->format('M d, H:i:s') : 'Pending' }}
              </td>
              <td class="px-6 py-4 text-center">
                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-bold border {{ $comp->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($comp->status === 'paused' ? 'bg-slate-100 text-slate-600 border-slate-200' : 'bg-red-50 text-red-700 border-red-100') }}">
                  <span class="w-1.5 h-1.5 rounded-full {{ $comp->status === 'active' ? 'bg-emerald-500' : ($comp->status === 'paused' ? 'bg-slate-400' : 'bg-red-500') }}"></span>
                  <span>{{ strtoupper($comp->status) }}</span>
                </span>
              </td>
              <td class="px-6 py-4 text-right space-x-1.5">
                <button 
                  wire:click="scrapeSync({{ $comp->id }})"
                  wire:loading.attr="disabled"
                  id="btn-scrape-sync-{{ $comp->id }}"
                  class="px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-[11px] font-semibold transition-colors duration-150 disabled:opacity-50">
                  Scrape Sync
                </button>
                <button 
                  wire:click="queueJob({{ $comp->id }})"
                  wire:loading.attr="disabled"
                  id="btn-queue-job-{{ $comp->id }}"
                  class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded text-[11px] font-semibold transition-colors duration-150 disabled:opacity-50">
                  Queue Job
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                No competitors configured for this workspace. Add competitors from the dashboard first!
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
