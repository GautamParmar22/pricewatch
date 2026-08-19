<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CompetitorController;
use App\Http\Controllers\SettingsController;
use App\Livewire\AlertSettings;
use App\Livewire\BillingManager;
use App\Livewire\ChangesLog;
use App\Livewire\CompetitorList;
use App\Livewire\Dashboard;
use App\Livewire\PriceHistoryChart;
use Illuminate\Support\Facades\Route;

// GP - 19-08-2026 code comment - Pricing platform web routing map

// Marketing
Route::get('/', function () {
    return view('welcome');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:10,1');
    
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Scoped SaaS Application Area
Route::middleware(['auth', \App\Http\Middleware\ScopeWorkspace::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Competitors CRUD
    Route::get('/competitors', CompetitorList::class)->name('competitors.index');
    Route::get('/competitors/{id}', [CompetitorController::class, 'show'])->name('competitors.show');
    Route::delete('/competitors/{id}', [CompetitorController::class, 'destroy'])->name('competitors.destroy');
    
    // Pricing Changes
    Route::get('/changes', ChangesLog::class)->name('changes.index');
    
    // Price history comparative charts
    Route::get('/price-history', PriceHistoryChart::class)->name('history.index');
    
    // Alerts connected channels & webhook registers
    Route::get('/alerts', AlertSettings::class)->name('alerts.index');
    
    // Settings profile
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/workspace', [SettingsController::class, 'updateWorkspace'])->name('settings.workspace');
    
    // Billing meters and subscriptions switcher
    Route::get('/billing', BillingManager::class)->name('billing.index');
});
