@section('title', 'Good Morning, ' . Auth::user()->name)
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Good morning, {{ Auth::user()->name }} 👋</h2>
      <p class="text-sm text-gray-500">Here's what's happening with your competitors.</p>
    </div>
    <a href="{{ route('competitors.index', ['add' => 1]) }}" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 rounded-lg shadow-sm hover-lift flex items-center gap-1.5">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
      Add Competitor
    </a>
  </div>

  <!-- KPI metrics -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs">
      <div class="flex items-center justify-between text-gray-400 mb-2">
        <span class="text-xs font-bold uppercase tracking-wider">Competitors</span>
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
      </div>
      <div class="flex items-baseline gap-2">
        <span class="text-3xl font-extrabold text-gray-950">{{ $activeCompetitors }}</span>
        <span class="text-xs font-semibold text-emerald-600">Active Monitored</span>
      </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs">
      <div class="flex items-center justify-between text-gray-400 mb-2">
        <span class="text-xs font-bold uppercase tracking-wider">Changes Detected</span>
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
      </div>
      <div class="flex items-baseline gap-2">
        <span class="text-3xl font-extrabold text-gray-950">{{ $totalChanges }}</span>
        <span class="text-xs font-semibold text-emerald-600">Total snapshot events</span>
      </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs">
      <div class="flex items-center justify-between text-gray-400 mb-2">
        <span class="text-xs font-bold uppercase tracking-wider">Price Increases</span>
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
      </div>
      <div class="flex items-baseline gap-2">
        <span class="text-3xl font-extrabold text-gray-950">{{ $priceIncreases }}</span>
        <span class="text-xs font-semibold text-red-600">Upward deviations</span>
      </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs">
      <div class="flex items-center justify-between text-gray-400 mb-2">
        <span class="text-xs font-bold uppercase tracking-wider">Unreviewed Alerts</span>
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
      </div>
      <div class="flex items-baseline gap-2">
        <span class="text-3xl font-extrabold text-gray-950">{{ $activeAlerts }}</span>
        <span class="text-xs font-semibold text-amber-600">Require attention</span>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Price movement trend chart -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
      <div class="flex items-center justify-between border-b border-gray-50 pb-3">
        <h3 class="font-extrabold text-gray-900 text-lg">Competitor Pricing Trend</h3>
        <div class="flex gap-1 bg-gray-100 p-0.5 rounded-lg text-[10px] font-bold text-gray-500">
          <button class="px-2 py-1 hover:text-indigo-600 transition-colors">30 Days</button>
        </div>
      </div>
      <div class="h-64 relative" id="chart-wrapper" x-init="
        new Chart(document.getElementById('dashboard-trend-chart'), {
          type: 'line',
          data: {
            labels: ['May', 'Jun', 'Jul', 'Aug'],
            datasets: [
              {
                label: 'Stripe Pro',
                data: [39, 39, 49, 59],
                borderColor: '#6366F1',
                tension: 0.3,
                borderWidth: 2,
                fill: false
              },
              {
                label: 'Paddle Growth',
                data: [69, 79, 79, 89],
                borderColor: '#10B981',
                tension: 0.3,
                borderWidth: 2,
                fill: false
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: { ticks: { callback: v => '$' + v } }
            }
          }
        });
      ">
        <canvas id="dashboard-trend-chart"></canvas>
      </div>
    </div>

    <!-- Competitor Overview summary cards -->
    <div class="lg:col-span-1 bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
      <div class="flex items-center justify-between border-b border-gray-50 pb-3">
        <h3 class="font-extrabold text-gray-900 text-lg">Competitor Overview</h3>
        <a href="{{ route('competitors.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline">View All</a>
      </div>
      <div class="space-y-3">
        @forelse ($competitorOverview as $c)
          <div class="bg-white p-5 rounded-xl border border-gray-100 hover:shadow-sm transition-all cursor-pointer" onclick="window.location.href='{{ route('competitors.show', $c->id) }}'">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center">{{ substr($c->name, 0, 1) }}</span>
                <div>
                  <h4 class="font-bold text-gray-900">{{ $c->name }}</h4>
                  <span class="text-xs text-gray-400">{{ parse_url($c->website_url, PHP_URL_HOST) }}</span>
                </div>
              </div>
              <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $c->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100' }} border">{{ ucfirst($c->status) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm border-t border-gray-50 pt-2 mt-2">
              <div>
                <span class="text-gray-400 text-[10px] block uppercase tracking-wider">Interval</span>
                <span class="font-semibold text-gray-900 text-xs">{{ $c->check_frequency }}</span>
              </div>
              <div>
                <span class="text-gray-400 text-[10px] block uppercase tracking-wider">Last checked</span>
                <span class="font-semibold text-gray-600 text-xs">{{ $c->last_checked_at?->diffForHumans() ?? 'Never' }}</span>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center py-8 text-gray-500">No competitors registered. <a href="{{ route('competitors.index', ['add' => 1]) }}" class="text-indigo-600 font-bold">Add one</a></div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Recent Changes Table -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
      <h3 class="font-extrabold text-gray-900 text-lg">Recent Pricing Changes</h3>
      <a href="{{ route('changes.index') }}" class="text-xs text-indigo-600 font-semibold hover:underline">View Change Log</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-400 text-xs font-bold uppercase">
            <th class="px-6 py-3">Competitor</th>
            <th class="px-6 py-3">Plan</th>
            <th class="px-6 py-3">Change Type</th>
            <th class="px-6 py-3">Previous</th>
            <th class="px-6 py-3">Current</th>
            <th class="px-6 py-3">Change %</th>
            <th class="px-6 py-3">Detected</th>
            <th class="px-6 py-3">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 text-xs">
          @forelse ($recentChanges as $rc)
            <tr class="hover:bg-gray-50/70 transition-colors cursor-pointer border-b border-gray-100" onclick="window.location.href='{{ route('changes.index') }}'">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $rc->competitor->name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $rc->plan?->name ?? '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $rc->change_type === 'price_increased' ? 'bg-red-50 text-red-700 border-red-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100' }} border">
                  {{ ucwords(str_replace('_', ' ', $rc->change_type)) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-400 line-through">{{ $rc->old_value ?? '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-bold">{{ $rc->new_value ?? '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap font-bold {{ $rc->percentage_change > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ $rc->percentage_change ? ($rc->percentage_change > 0 ? '+' : '') . $rc->percentage_change . '%' : '—' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $rc->detected_at->diffForHumans() }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-0.5 text-[10px] font-bold {{ $rc->reviewed_at ? 'bg-gray-50 text-gray-500 border-gray-200' : 'bg-indigo-50 text-indigo-700 border-indigo-100' }} border rounded">
                  {{ $rc->reviewed_at ? 'Reviewed' : 'New' }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-6 py-12 text-center text-gray-500">No price changes detected yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
