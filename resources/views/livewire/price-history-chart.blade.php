@section('title', 'Price Trends History')

<div class="space-y-6">
  <div>
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Price History</h2>
    <p class="text-sm text-gray-500">Track how competitor pricing changes over time.</p>
  </div>

  <!-- Controls -->
  <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-wrap gap-4 items-center">
    <div class="w-full sm:w-auto">
      <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Competitor</label>
      <select wire:model.live="competitorId" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-white focus:outline-none w-full sm:w-48">
        <option value="all">All Competitors</option>
        @foreach ($competitors as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <!-- Chart card -->
  <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
    <h3 class="font-extrabold text-gray-950 text-lg border-b border-gray-50 pb-3">Price Comparison Timeline</h3>
    <div class="h-80 relative" x-data="{ chart: null }" x-init="
      chart = new Chart(document.getElementById('history-large-chart'), {
        type: 'line',
        data: {
          labels: ['May', 'June', 'July', 'August'],
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
          maintainAspectRatio: false
        }
      });
    ">
      <canvas id="history-large-chart"></canvas>
    </div>
  </div>

  <!-- Log table -->
  <div class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
      <h3 class="font-extrabold text-gray-950 text-lg">Historical Changes Log</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 text-xs font-bold uppercase">
            <th class="px-6 py-3">Date</th>
            <th class="px-6 py-3">Competitor</th>
            <th class="px-6 py-3">Plan</th>
            <th class="px-6 py-3">Old Price</th>
            <th class="px-6 py-3">New Price</th>
            <th class="px-6 py-3">Change %</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 text-gray-600 text-xs">
          @forelse ($historyLog as $hl)
            <tr class="border-b border-gray-100 hover:bg-gray-50/50">
              <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $hl->detected_at->format('F d, Y') }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-950 font-semibold">{{ $hl->competitor->name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-medium">{{ $hl->plan?->name ?? '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-400 line-through">{{ $hl->old_value ?? '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-bold">{{ $hl->new_value ?? '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap font-extrabold {{ $hl->percentage_change > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ ($hl->percentage_change > 0 ? '+' : '') . $hl->percentage_change . '%' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center text-gray-500">No historical change logs stored.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
