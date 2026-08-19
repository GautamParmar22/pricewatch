@extends('layouts.app')

@section('title', 'Competitor Insights — ' . $competitor->name)

@section('content')
<div class="space-y-6">
  <!-- Back button -->
  <a href="{{ route('competitors.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:underline">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    Back to Competitors
  </a>

  <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-gray-100 pb-5">
    <div class="flex items-center gap-4">
      <span class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 font-extrabold text-xl flex items-center justify-center">
        {{ substr($competitor->name, 0, 1) }}
      </span>
      <div>
        <h2 class="text-3xl font-extrabold text-gray-950">{{ $competitor->name }}</h2>
        <a href="{{ $competitor->website_url }}" target="_blank" class="text-xs text-indigo-600 font-medium hover:underline flex items-center gap-1 mt-0.5">
          {{ parse_url($competitor->website_url, PHP_URL_HOST) }}
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        </a>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $competitor->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }} border">
        {{ ucfirst($competitor->status) }}
      </span>
      
      <!-- Pause / Resume -->
      <form action="{{ route('competitors.destroy', $competitor->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this competitor?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-xs font-semibold px-4 py-2 bg-red-50 text-red-600 border border-red-100 rounded-lg hover:bg-red-100 transition-colors">
          Delete Tracker
        </button>
      </form>
    </div>
  </div>

  <!-- Metric Cards -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    <div class="bg-white p-5 rounded-xl border border-gray-100">
      <span class="text-xs text-gray-400 block mb-1">Scrape Frequency</span>
      <span class="text-lg font-bold text-gray-950 capitalize">{{ $competitor->check_frequency }}</span>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-100">
      <span class="text-xs text-gray-400 block mb-1">Plans Monitored</span>
      <span class="text-lg font-bold text-gray-950">{{ count($plans) }} Tiers</span>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-100">
      <span class="text-xs text-gray-400 block mb-1">Last Checked</span>
      <span class="text-lg font-bold text-gray-950 text-gray-600">{{ $competitor->last_checked_at?->diffForHumans() ?? 'Never' }}</span>
    </div>
    <div class="bg-white p-5 rounded-xl border border-gray-100">
      <span class="text-xs text-gray-400 block mb-1">Next Scheduled Scrape</span>
      <span class="text-lg font-bold text-indigo-600 text-xs">{{ $competitor->next_check_at?->format('M d, H:i') ?? 'Pending' }}</span>
    </div>
  </div>

  <!-- Visuals container -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left: Current Plans table -->
    <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
      <h3 class="font-extrabold text-gray-950 text-lg border-b border-gray-50 pb-3">Active Pricing Plans</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 text-xs font-bold uppercase">
              <th class="px-6 py-3">Plan</th>
              <th class="px-6 py-3">Monthly</th>
              <th class="px-6 py-3">Annual</th>
              <th class="px-6 py-3">Users Limit</th>
              <th class="px-6 py-3">Features</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50 text-gray-600 text-xs">
            @forelse ($plans as $p)
              <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $p->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">${{ number_format($p->monthly_price, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($p->annual_price, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $p->user_limit === 999999 ? 'Unlimited' : ($p->user_limit ?? '—') }}</td>
                <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">
                  @if($p->features)
                    {{ implode(', ', $p->features) }}
                  @else
                    No features extracted.
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No active pricing plans captured yet. Scraper baseline job is running...</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Right: Change History Timeline -->
    <div class="lg:col-span-1 bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
      <h3 class="font-extrabold text-gray-950 text-lg border-b border-gray-50 pb-3">Recent Timeline</h3>
      <div class="relative pl-2 space-y-6">
        @forelse ($changes as $c)
          <div class="relative pl-6 pb-6 last:pb-0">
            <span class="absolute left-0 top-1.5 w-3 h-3 rounded-full bg-indigo-500 ring-4 ring-indigo-50"></span>
            <div class="text-[10px] text-gray-400 font-semibold mb-1">{{ $c->detected_at->diffForHumans() }}</div>
            <div class="text-xs font-bold text-gray-900 mb-1 hover:underline cursor-pointer">{{ ucwords(str_replace('_', ' ', $c->change_type)) }}: {{ $c->plan?->name ?? 'Tier' }}</div>
            <div class="text-xs text-gray-600">Old: <span class="line-through">{{ $c->old_value ?? '—' }}</span> &rarr; New: <span class="font-bold text-red-600">{{ $c->new_value ?? '—' }}</span></div>
          </div>
        @empty
          <div class="text-xs text-gray-500 py-4">No recent changes detected for this competitor.</div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- History Chart -->
  <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
    <h3 class="font-extrabold text-gray-950 text-lg border-b border-gray-50 pb-3">Price Trend Analysis</h3>
    <div class="h-64 relative" x-init="
      new Chart(document.getElementById('comp-detail-history-chart'), {
        type: 'line',
        data: {
          labels: ['May', 'Jun', 'Jul', 'Aug'],
          datasets: [{
            label: 'Plan Monthly Price ($)',
            data: [39, 39, 49, 59],
            borderColor: '#6366F1',
            tension: 0.3,
            borderWidth: 2,
            fill: false
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      });
    ">
      <canvas id="comp-detail-history-chart"></canvas>
    </div>
  </div>
</div>
@endsection
