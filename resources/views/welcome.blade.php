<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PriceWatch — Competitor Pricing Intelligence Platform</title>
  <meta name="description" content="PriceWatch automatically monitors competitors' pricing pages, detects plan changes, stores snapshots, and alerts you instantly.">
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Custom styles -->
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
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
<body class="bg-[#F8FAFC] text-[#111827] min-h-screen flex flex-col">

  <!-- Navbar -->
  <header class="sticky top-0 z-40 w-full glass-header py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
      <a href="/" class="flex items-center gap-2 text-indigo-600 font-extrabold text-xl tracking-tight">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
        </svg>
        <span>PriceWatch</span>
      </a>
      <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
        <a href="#features" class="hover:text-indigo-600 transition-colors">Features</a>
        <a href="#how-it-works" class="hover:text-indigo-600 transition-colors">How it Works</a>
        <a href="#pricing" class="hover:text-indigo-600 transition-colors">Pricing</a>
      </nav>
      <div class="flex items-center gap-4">
        @auth
          <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors">Go to Dashboard</a>
        @else
          <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-indigo-600 transition-colors">Sign In</a>
          <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg transition-colors">Start Free Trial</a>
        @endauth
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="relative pt-20 pb-24 overflow-hidden bg-grid-pattern">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 mb-6">
        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-ping"></span>
        Production SaaS active
      </span>
      <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 tracking-tight max-w-4xl mx-auto leading-[1.1]">
        Know when your competitors change their prices.
      </h1>
      <p class="mt-6 text-xl text-gray-600 max-w-2xl mx-auto font-light leading-relaxed">
        PriceWatch automatically monitors competitor pricing pages, detects changes, and alerts your team before you miss an important pricing move.
      </p>
      <div class="mt-10 flex flex-wrap justify-center gap-4">
        @auth
          <a href="{{ route('dashboard') }}" class="text-base font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-lg shadow-sm hover-lift">
            Go to Dashboard
          </a>
        @else
          <a href="{{ route('register') }}" class="text-base font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-lg shadow-sm hover-lift">
            Start Monitoring Free
          </a>
          <a href="{{ route('login') }}" class="text-base font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 px-6 py-3 rounded-lg shadow-xs hover-lift">
            View Live Demo
          </a>
        @endauth
      </div>

      <!-- Hero Visual Preview Container -->
      <div class="mt-16 relative mx-auto max-w-5xl rounded-2xl border border-gray-200 bg-white shadow-2xl p-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 bg-red-400 rounded-full"></span>
            <span class="w-3 h-3 bg-yellow-400 rounded-full"></span>
            <span class="w-3 h-3 bg-green-400 rounded-full"></span>
            <span class="ml-2 text-xs font-semibold text-gray-400">PriceWatch Live System Preview</span>
          </div>
          <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">Live</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Left Side: Competitor Pricing Animation Card -->
          <div class="md:col-span-1 bg-gray-50 p-6 rounded-xl border border-gray-100 flex flex-col justify-between min-h-[220px]">
            <div class="flex items-center justify-between">
              <span class="flex items-center gap-2 font-bold text-gray-800 text-sm">
                <span class="w-6 h-6 rounded bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">S</span>
                Stripe Monitored
              </span>
              <span class="badge-anim inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                +20% Increase
              </span>
            </div>
            <div class="my-6">
              <span class="text-xs text-gray-400 block font-medium">Professional Tier Pricing</span>
              <div class="flex items-baseline gap-2 mt-1">
                <span class="strike-anim text-2xl font-bold text-gray-400 line-through">$49</span>
                <span class="price-anim text-3xl font-extrabold text-gray-900"></span>
                <span class="text-xs text-gray-500 font-medium">/ month</span>
              </div>
            </div>
            <p class="text-xs text-gray-500 leading-normal">
              Continuous monitoring active. Historical snap stored.
            </p>
          </div>

          <!-- Right Side: Mini Table comparison & metrics -->
          <div class="md:col-span-2 space-y-4">
            <div class="grid grid-cols-3 gap-4">
              <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="text-xs text-gray-400 block">Competitors Monitored</span>
                <span class="text-lg font-bold text-gray-900">24</span>
              </div>
              <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="text-xs text-gray-400 block">Changes Detected</span>
                <span class="text-lg font-bold text-gray-900">18</span>
              </div>
              <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="text-xs text-gray-400 block">Active Alerts</span>
                <span class="text-lg font-bold text-gray-900">6</span>
              </div>
            </div>

            <div class="border border-gray-100 rounded-xl overflow-hidden bg-white">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-gray-50 text-gray-400 text-xs font-bold uppercase border-b border-gray-100">
                    <th class="px-4 py-2">Competitor</th>
                    <th class="px-4 py-2">Change</th>
                    <th class="px-4 py-2">Date</th>
                  </tr>
                </thead>
                <tbody class="text-xs text-gray-600">
                  <tr class="border-b border-gray-50">
                    <td class="px-4 py-3 font-semibold text-gray-900">Stripe</td>
                    <td class="px-4 py-3 text-red-600 font-bold">$49 &rarr; $59</td>
                    <td class="px-4 py-3">2 hours ago</td>
                  </tr>
                  <tr class="border-b border-gray-50">
                    <td class="px-4 py-3 font-semibold text-gray-900">Paddle</td>
                    <td class="px-4 py-3 text-red-600 font-bold">$79 &rarr; $89</td>
                    <td class="px-4 py-3">Yesterday</td>
                  </tr>
                  <tr>
                    <td class="px-4 py-3 font-semibold text-gray-900">Lemon Squeezy</td>
                    <td class="px-4 py-3 text-emerald-600 font-bold">$32 &rarr; $29</td>
                    <td class="px-4 py-3">3 days ago</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Trusted By -->
  <section class="py-12 border-y border-gray-100 bg-white">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Built for SaaS founders, product teams, and competitive intelligence teams</p>
      <div class="mt-8 flex flex-wrap justify-center items-center gap-12 md:gap-20 opacity-50 grayscale hover:grayscale-0 transition-all">
        <span class="font-extrabold text-xl tracking-tight text-gray-900 flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
          StackCorp
        </span>
        <span class="font-extrabold text-xl tracking-tight text-gray-900 flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
          Signalyze
        </span>
        <span class="font-extrabold text-xl tracking-tight text-gray-900 flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/></svg>
          Vecta SaaS
        </span>
      </div>
    </div>
  </section>

  <!-- Problem Section -->
  <section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto">
        <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider">The Problem</h2>
        <p class="mt-3 text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
          Stop checking competitor pricing pages manually.
        </p>
      </div>

      <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-2xl border border-gray-200/60 shadow-xs hover-lift">
          <span class="inline-flex p-3 bg-red-50 text-red-600 rounded-lg mb-6 border border-red-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </span>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Manual monitoring wastes time</h3>
          <p class="text-sm text-gray-500 leading-relaxed">
            Having team members check dozen of competitors every week is repetitive, inefficient, and highly prone to human error.
          </p>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-gray-200/60 shadow-xs hover-lift">
          <span class="inline-flex p-3 bg-red-50 text-red-600 rounded-lg mb-6 border border-red-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
          </span>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Competitors change pricing without warning</h3>
          <p class="text-sm text-gray-500 leading-relaxed">
            New pricing tiers, feature exclusions, or usage limit shifts occur without announcements. You learn about them too late.
          </p>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-gray-200/60 shadow-xs hover-lift">
          <span class="inline-flex p-3 bg-red-50 text-red-600 rounded-lg mb-6 border border-red-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </span>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Historical pricing data disappears</h3>
          <p class="text-sm text-gray-500 leading-relaxed">
            When a page changes, the old version vanishes. You lose critical insights into their long-term pricing iterations.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section id="how-it-works" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16">
        <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider">Process Flow</h2>
        <p class="mt-3 text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
          Pricing intelligence in three simple steps.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center">
          <div class="w-12 h-12 bg-indigo-50 text-indigo-600 font-extrabold text-lg flex items-center justify-center rounded-full mx-auto mb-6 border border-indigo-100">1</div>
          <h4 class="font-bold text-gray-900 text-lg mb-2">Add Competitor</h4>
          <p class="text-sm text-gray-500 max-w-xs mx-auto leading-relaxed">Enter any pricing URL. PriceWatch crawls it and creates an active baseline snapshot.</p>
        </div>

        <div class="text-center">
          <div class="w-12 h-12 bg-indigo-50 text-indigo-600 font-extrabold text-lg flex items-center justify-center rounded-full mx-auto mb-6 border border-indigo-100">2</div>
          <h4 class="font-bold text-gray-900 text-lg mb-2">PriceWatch Monitors the Page</h4>
          <p class="text-sm text-gray-500 max-w-xs mx-auto leading-relaxed">Our background queue jobs periodically inspect pricing page HTML, tracking differences safely.</p>
        </div>

        <div class="text-center">
          <div class="w-12 h-12 bg-indigo-50 text-indigo-600 font-extrabold text-lg flex items-center justify-center rounded-full mx-auto mb-6 border border-indigo-100">3</div>
          <h4 class="font-bold text-gray-900 text-lg mb-2">Get Notified Instantly</h4>
          <p class="text-sm text-gray-500 max-w-xs mx-auto leading-relaxed">Get alert triggers on Email, Slack or Webhooks when plans, features or prices move.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Grid -->
  <section id="features" class="py-24 bg-slate-50 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16">
        <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider">Features</h2>
        <p class="mt-3 text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
          Everything you need to out-price the market.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200/50 shadow-xs hover-lift">
          <h4 class="font-bold text-gray-950 mb-2">Automatic Monitoring</h4>
          <p class="text-sm text-gray-500 leading-normal">Cloud bots visit target sites on schedules you control. Fully automated.</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200/50 shadow-xs hover-lift">
          <h4 class="font-bold text-gray-950 mb-2">Price Change Detection</h4>
          <p class="text-sm text-gray-500 leading-normal">Instantly identifies upward and downward changes down to the dollar.</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200/50 shadow-xs hover-lift">
          <h4 class="font-bold text-gray-950 mb-2">Historical Snapshots</h4>
          <p class="text-sm text-gray-500 leading-normal">View pricing configuration changes over multi-month charts for deep trend analysis.</p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200/50 shadow-xs hover-lift">
          <h4 class="font-bold text-gray-950 mb-2">Competitor Comparison</h4>
          <p class="text-sm text-gray-500 leading-normal">Compare basic/advanced plan tiers side-by-side inside structured charts.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Grid -->
  <section id="pricing" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-16">
        <h2 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider">Pricing</h2>
        <p class="mt-3 text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">
          Simple, scale-ready pricing models.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
        <!-- Starter -->
        <div class="bg-white p-8 rounded-2xl border border-gray-200/70 shadow-sm flex flex-col justify-between">
          <div>
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Starter</h4>
            <div class="mt-4 flex items-baseline text-gray-900">
              <span class="text-4xl font-extrabold tracking-tight">$19</span>
              <span class="ml-1 text-xl font-semibold text-gray-400">/mo</span>
            </div>
            <p class="mt-4 text-sm text-gray-500">Perfect for bootstrapped SaaS founders tracking main rivals.</p>
            <ul class="mt-6 space-y-4 text-sm text-gray-600 border-t border-gray-50 pt-6">
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Track up to 10 competitors
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Daily checks frequency
              </li>
            </ul>
          </div>
          <a href="{{ route('register') }}" class="mt-8 w-full py-2 px-4 text-center text-sm font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100 transition-colors block">Start Starter Trial</a>
        </div>

        <!-- Growth (Recommended) -->
        <div class="bg-white p-8 rounded-2xl border-2 border-indigo-600 shadow-md flex flex-col justify-between relative transform -translate-y-2">
          <span class="absolute top-0 right-1/2 translate-x-1/2 -translate-y-1/2 px-3 py-0.5 rounded-full text-xs font-semibold text-white bg-indigo-600 uppercase tracking-wider">Recommended</span>
          <div>
            <h4 class="text-sm font-semibold text-indigo-600 uppercase tracking-wider">Growth</h4>
            <div class="mt-4 flex items-baseline text-gray-900">
              <span class="text-4xl font-extrabold tracking-tight">$49</span>
              <span class="ml-1 text-xl font-semibold text-gray-400">/mo</span>
            </div>
            <p class="mt-4 text-sm text-gray-500">Ideal for scaling companies and proactive product managers.</p>
            <ul class="mt-6 space-y-4 text-sm text-gray-600 border-t border-gray-50 pt-6">
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Track up to 25 competitors
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Hourly monitoring frequency
              </li>
            </ul>
          </div>
          <a href="{{ route('register') }}" class="mt-8 w-full py-2.5 px-4 text-center text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors block">Start Growth Trial</a>
        </div>

        <!-- Pro -->
        <div class="bg-white p-8 rounded-2xl border border-gray-200/70 shadow-sm flex flex-col justify-between">
          <div>
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Pro</h4>
            <div class="mt-4 flex items-baseline text-gray-900">
              <span class="text-4xl font-extrabold tracking-tight">$99</span>
              <span class="ml-1 text-xl font-semibold text-gray-400">/mo</span>
            </div>
            <p class="mt-4 text-sm text-gray-500">Perfect for marketing teams and enterprises needing custom APIs.</p>
            <ul class="mt-6 space-y-4 text-sm text-gray-600 border-t border-gray-50 pt-6">
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Track up to 100 competitors
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                Instant Webhook pushes
              </li>
            </ul>
          </div>
          <a href="{{ route('register') }}" class="mt-8 w-full py-2 px-4 text-center text-sm font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100 transition-colors block">Start Pro Trial</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Landing Footer -->
  <footer class="bg-gray-900 text-gray-400 py-12 border-t border-gray-800 mt-auto">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between text-sm">
      <div class="flex items-center gap-2 text-white font-extrabold text-lg">
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        PriceWatch
      </div>
      <p class="mt-4 md:mt-0">&copy; 2026 PriceWatch Inc. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
