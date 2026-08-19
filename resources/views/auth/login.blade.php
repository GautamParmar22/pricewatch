<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — PriceWatch</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="bg-[#F8FAFC] text-[#111827] min-h-screen flex">
  
  <div class="w-full flex flex-col lg:flex-row">
    <!-- Left Column (Brand/Testimonial Panel) -->
    <div class="hidden lg:flex lg:w-1/2 bg-indigo-900 text-indigo-100 p-12 flex-col justify-between relative bg-grid-pattern overflow-hidden border-r border-indigo-800">
      <div class="absolute inset-0 bg-indigo-950 opacity-90 z-0"></div>
      <div class="relative z-10">
        <a href="/" class="flex items-center gap-2 text-white font-extrabold text-2xl tracking-tight">
          <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
          <span>PriceWatch</span>
        </a>
      </div>
      <div class="relative z-10 my-auto max-w-md">
        <blockquote class="text-xl font-light italic leading-relaxed text-white">
          "Before PriceWatch, we checked competitor sites manually every Friday. We missed Stripe's billing structure adjustment by 3 weeks. Now we respond to price moves on the same afternoon."
        </blockquote>
        <div class="mt-6 flex items-center gap-3">
          <span class="w-10 h-10 rounded-full bg-indigo-800 border border-indigo-700 flex items-center justify-center font-bold text-white">AR</span>
          <div>
            <div class="text-sm font-semibold text-white">Alex Rivera</div>
            <div class="text-xs text-indigo-300">VP Marketing, Signalyze</div>
          </div>
        </div>
      </div>
      <div class="relative z-10 text-xs text-indigo-300 flex items-center gap-6">
        <span>Privacy Policy</span>
        <span>Terms of Service</span>
      </div>
    </div>

    <!-- Right Column (Form container) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
      <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <div>
          <div class="flex items-center gap-2 text-indigo-600 font-bold text-xl mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <span>PriceWatch</span>
          </div>
          <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Sign in to your account</h2>
          <p class="mt-2 text-sm text-gray-600">
            Or
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">start your 14-day free trial</a>
          </p>
        </div>

        @if ($errors->any())
          <div class="bg-red-50 text-red-700 p-4 rounded-xl border border-red-100 text-sm">
            <ul class="list-disc pl-4 space-y-1">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
          @csrf
          <div class="space-y-4">
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Work Email</label>
              <input id="email" name="email" type="email" autocomplete="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="alex@company.com" value="{{ old('email') }}">
            </div>
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="••••••••">
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
              <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot your password?</a>
            </div>
          </div>

          <div class="space-y-3">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Sign In
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</body>
</html>
