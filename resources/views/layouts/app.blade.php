<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'PriceWatch Dashboard')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Custom styles -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
  
  @livewireStyles
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            indigo: {
              50: '#EEF2FF',
              100: '#E0E7FF',
              200: '#C7D2FE',
              500: '#6366F1',
              600: '#4F46E5',
              700: '#4338CA',
            },
            slate: {
              50: '#F8FAFC',
              100: '#F1F5F9',
              200: '#E2E8F0',
              300: '#CBD5E1',
              900: '#111827',
            }
          }
        }
      }
    }
  </script>
</head>
<body class="bg-[#F8FAFC] text-[#111827] min-h-screen flex flex-col" x-data="{ userMenuOpen: false, notifMenuOpen: false }">

  <div class="flex flex-1 flex-col md:flex-row min-h-screen">
    
    <!-- Left Sidebar Menu -->
    <aside class="w-full md:w-64 bg-white border-r border-gray-200 flex flex-col justify-between shrink-0">
      <div>
        <!-- Workspace Brand Header -->
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
          <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-indigo-600 font-extrabold text-lg tracking-tight">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <span>PriceWatch</span>
          </a>
          <span class="text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded border border-emerald-100">Live</span>
        </div>

        <!-- Sidebar Navigation Menu Items -->
        <nav class="p-4 space-y-1">
          <a href="{{ route('dashboard') }}" class="flex items-center gap-3 text-sm px-3 py-2 rounded-lg transition-colors font-medium {{ Request::routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
            Dashboard
          </a>
          <a href="{{ route('competitors.index') }}" class="flex items-center gap-3 text-sm px-3 py-2 rounded-lg transition-colors font-medium {{ Request::routeIs('competitors.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            Competitors
          </a>
          <a href="{{ route('changes.index') }}" class="flex items-center gap-3 text-sm px-3 py-2 rounded-lg transition-colors font-medium {{ Request::routeIs('changes.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Changes
          </a>
          <a href="{{ route('history.index') }}" class="flex items-center gap-3 text-sm px-3 py-2 rounded-lg transition-colors font-medium {{ Request::routeIs('history.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Price History
          </a>
          <a href="{{ route('alerts.index') }}" class="flex items-center gap-3 text-sm px-3 py-2 rounded-lg transition-colors font-medium {{ Request::routeIs('alerts.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            Alerts
          </a>
          <a href="{{ route('settings.index') }}" class="flex items-center gap-3 text-sm px-3 py-2 rounded-lg transition-colors font-medium {{ Request::routeIs('settings.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Settings
          </a>
          <a href="{{ route('billing.index') }}" class="flex items-center gap-3 text-sm px-3 py-2 rounded-lg transition-colors font-medium {{ Request::routeIs('billing.*') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            Billing
          </a>
          <a href="{{ route('dev.scraper') }}" class="flex items-center gap-3 text-sm px-3 py-2 rounded-lg transition-colors font-medium {{ Request::routeIs('dev.scraper') ? 'bg-indigo-50 text-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
            Scraper DevTools
          </a>
        </nav>
      </div>

      <!-- Workspace profile menu and selector bottom -->
      <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2.5">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
            <span class="text-xs font-semibold text-gray-500">{{ App\Support\TenantContext::getWorkspace()?->name ?? 'Acme Workspace' }}</span>
          </div>
          <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded px-1">ACTIVE</span>
        </div>
        <div class="flex items-center gap-3">
          <span class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center border border-indigo-200">
            {{ substr(Auth::user()->name, 0, 2) }}
          </span>
          <div class="flex-1 min-w-0">
            <div class="text-xs font-bold text-gray-900 truncate">{{ Auth::user()->name }}</div>
            <div class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</div>
          </div>
          <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-gray-400 hover:text-gray-600 p-1" title="Sign Out">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
          </form>
        </div>
      </div>
    </aside>

    <!-- Main Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
      
      <!-- Top header navigation -->
      <header class="glass-header h-16 flex items-center justify-between px-6 sticky top-0 z-30 shrink-0">
        <!-- Search bar -->
        <div class="w-full max-w-md hidden sm:block relative">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </span>
          <input type="text" placeholder="Search competitors, plans or alerts..." class="w-full pl-9 pr-4 py-1.5 bg-gray-50/70 border border-gray-200/80 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:bg-white transition-colors">
        </div>

        <div class="flex items-center gap-4 ml-auto">
          <!-- Notification Icon -->
          <div class="relative">
            <button @click="notifMenuOpen = !notifMenuOpen" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-colors relative">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
              @php
                $unreviewedCount = \App\Models\PriceChange::where('workspace_id', \App\Support\TenantContext::getWorkspaceId())
                  ->whereNull('reviewed_at')
                  ->count();
              @endphp
              @if ($unreviewedCount > 0)
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
              @endif
            </button>
            
            <!-- Dropdown notifications -->
            <div x-show="notifMenuOpen" @click.away="notifMenuOpen = false" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 p-4 space-y-3 z-50" style="display: none;">
              <div class="flex items-center justify-between border-b border-gray-50 pb-2">
                <span class="text-xs font-bold text-gray-900">Unreviewed Changes</span>
                <span class="text-[10px] text-gray-500 font-semibold">{{ $unreviewedCount }} new</span>
              </div>
              <div class="space-y-2 text-xs max-h-60 overflow-y-auto">
                @php
                  $latestChanges = \App\Models\PriceChange::where('workspace_id', \App\Support\TenantContext::getWorkspaceId())
                    ->whereNull('reviewed_at')
                    ->latest()
                    ->take(5)
                    ->get();
                @endphp
                @forelse ($latestChanges as $lc)
                  <a href="{{ route('changes.index') }}" class="block p-2 hover:bg-gray-50 rounded-lg">
                    <span class="font-bold text-gray-900 block">{{ $lc->competitor->name }} ({{ $lc->plan?->name ?? 'Plan' }})</span>
                    <span class="text-gray-500">Changed from {{ $lc->old_value ?? '—' }} to {{ $lc->new_value ?? '—' }}</span>
                    <span class="text-[10px] text-indigo-600 block mt-1">{{ $lc->detected_at->diffForHumans() }}</span>
                  </a>
                @empty
                  <div class="text-center py-4 text-gray-500">No unreviewed changes.</div>
                @endforelse
              </div>
            </div>
          </div>

          <hr class="w-px h-6 bg-gray-200">

          <!-- User Menu Dropdown -->
          <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
              <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center border border-indigo-200">
                {{ substr(Auth::user()->name, 0, 2) }}
              </span>
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <!-- Dropdown -->
            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 p-2 space-y-1 z-50" style="display: none;">
              <a href="{{ route('settings.index') }}" class="block px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 rounded-lg">Profile Settings</a>
              <a href="{{ route('billing.index') }}" class="block px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 rounded-lg">Billing & Usage</a>
              <hr class="my-1 border-gray-50">
              <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full text-left block px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-lg">Logout</button>
              </form>
            </div>
          </div>
        </div>
      </header>

      <!-- Main view containers -->
      <main class="flex-1 p-6 max-w-7xl w-full mx-auto space-y-6">
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Slide-in Toast Container bottom right -->
  <div id="toast-container" class="fixed bottom-5 right-5 z-50 space-y-3 pointer-events-none">
    @if(session('success'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="toast-enter max-w-sm w-full bg-white shadow-lg rounded-xl border border-gray-100 p-4 flex items-center justify-between pointer-events-auto overflow-hidden">
        <div class="flex items-center gap-3">
          <span class="text-emerald-500 bg-emerald-50 p-1.5 rounded-lg border border-emerald-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </span>
          <div class="text-sm font-semibold text-gray-900">{{ session('success') }}</div>
        </div>
        <button class="text-gray-400 hover:text-gray-600 transition-colors p-1" @click="show = false">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
    @endif

    @if(session('info'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="toast-enter max-w-sm w-full bg-white shadow-lg rounded-xl border border-gray-100 p-4 flex items-center justify-between pointer-events-auto overflow-hidden">
        <div class="flex items-center gap-3">
          <span class="text-indigo-500 bg-indigo-50 p-1.5 rounded-lg border border-indigo-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </span>
          <div class="text-sm font-semibold text-gray-900">{{ session('info') }}</div>
        </div>
        <button class="text-gray-400 hover:text-gray-600 transition-colors p-1" @click="show = false">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
    @endif
  </div>

  @livewireScripts
</body>
</html>
