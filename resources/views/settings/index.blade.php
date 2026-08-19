@extends('layouts.app')

@section('title', 'Workspace & Account Settings')

@section('content')
<div class="space-y-6">
  <div>
    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">Settings</h2>
    <p class="text-sm text-gray-500">Configure your personal and workspace profile preferences.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile & Workspace Form settings -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- Profile form -->
      <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
        <h3 class="font-extrabold text-gray-950 text-lg border-b border-gray-50 pb-3">Profile Details</h3>
        <form action="{{ route('settings.profile') }}" method="POST" class="space-y-4">
          @csrf
          @method('PUT')
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="profile-name" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Name</label>
              <input id="profile-name" name="name" type="text" value="{{ $user->name }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white">
            </div>
            <div>
              <label for="profile-email" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</label>
              <input id="profile-email" name="email" type="email" value="{{ $user->email }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white">
            </div>
          </div>
          <button type="submit" class="text-xs font-semibold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors shadow-xs">Save Profile</button>
        </form>
      </div>

      <!-- Workspace form -->
      <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-xs space-y-4">
        <h3 class="font-extrabold text-gray-950 text-lg border-b border-gray-50 pb-3">Workspace Configurations</h3>
        <form action="{{ route('settings.workspace') }}" method="POST" class="space-y-4">
          @csrf
          @method('PUT')
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="workspace-company" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Company Name</label>
              <input id="workspace-company" name="company_name" type="text" value="{{ $workspace->name }}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white">
            </div>
            <div>
              <label for="workspace-url" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Workspace URL slug</label>
              <input id="workspace-url" type="text" value="{{ $workspace->slug }}" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-400 focus:outline-none">
            </div>
          </div>
          <button type="submit" class="text-xs font-semibold px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors shadow-xs">Save Workspace</button>
        </form>
      </div>
    </div>

    <!-- Danger zone settings -->
    <div class="lg:col-span-1 space-y-6">
      <div class="bg-white p-6 rounded-xl border border-red-100 shadow-xs space-y-4">
        <h3 class="font-extrabold text-red-700 text-lg border-b border-red-50 pb-3">Danger Zone</h3>
        <p class="text-xs text-gray-500">Delete your PriceWatch workspace, losing all historical price snap records.</p>
        <button onclick="alert('Workspace deletion requires customer support confirmation.')" class="w-full py-2 text-center text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Delete Workspace</button>
      </div>
    </div>
  </div>
</div>
@endsection
