<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Settings;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\CraneCatalog;
use App\Livewire\CraneDetail;
use App\Livewire\Owner\CreateRentalQuote;
use App\Livewire\Owner\CraneForm;
use App\Livewire\Owner\Dashboard as OwnerDashboard;
use App\Livewire\RentalRequest\CreateRentalRequest;
use App\Livewire\RentalRequest\MyRentalRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', CraneCatalog::class)->name('cranes.index');
Route::get('/cranes/{crane}', CraneDetail::class)->name('cranes.show');

Route::middleware(['guest', 'throttle:6,1'])->group(function (): void {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::post('/logout', function (Request $request): RedirectResponse {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return to_route('login');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:customer'])
    ->prefix('requests')
    ->as('requests.')
    ->group(function (): void {
        Route::get('/', MyRentalRequests::class)->name('mine');
        Route::get('/create', CreateRentalRequest::class)->name('create');
    });

Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->as('owner.')
    ->group(function (): void {
        Route::get('/', OwnerDashboard::class)->name('dashboard');
        Route::get('/cranes/create', CraneForm::class)->name('cranes.create');
        Route::get('/cranes/{crane}/edit', CraneForm::class)->name('cranes.edit');
        Route::get('/requests/{rentalRequest}/quotes/create', CreateRentalQuote::class)->name('quotes.create');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function (): void {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/settings', Settings::class)->name('settings');
    });
