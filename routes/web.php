<?php

use App\Livewire\Admin\Auth\LoginPage;
use App\Livewire\Admin\Auth\ForgotPasswordPage;
use App\Livewire\Admin\Auth\ResetPasswordPage;
use App\Livewire\Admin\DashboardPage;
use App\Livewire\Admin\Users\PendingApprovalsPage;
use App\Livewire\Admin\Users\UsersListPage;
use App\Livewire\Admin\Subscriptions\SubscriptionsListPage;
use App\Livewire\Admin\Businesses\StationsPage;
use App\Livewire\Admin\Businesses\CarWashesPage;
use App\Livewire\Admin\Businesses\MaintenancePage;
use App\Livewire\Admin\Security\SecurityLogsPage;
use App\Livewire\Admin\Tickets\TicketsPage;
use App\Livewire\Admin\Notifications\BroadcastPage;
use App\Livewire\Admin\Settings\SettingsPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', LoginPage::class)->name('login')->middleware('guest');
    Route::get('/forgot-password', ForgotPasswordPage::class)->name('password.forgot')->middleware('guest');
    Route::get('/reset-password', ResetPasswordPage::class)->name('password.reset')->middleware('guest');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', DashboardPage::class)->name('dashboard');
        Route::get('/users/pending', PendingApprovalsPage::class)->name('users.pending');
        Route::get('/users', UsersListPage::class)->name('users.index');
        Route::get('/subscriptions', SubscriptionsListPage::class)->name('subscriptions.index');
        Route::get('/stations', StationsPage::class)->name('stations.index');
        Route::get('/car-washes', CarWashesPage::class)->name('carwashes.index');
        Route::get('/maintenance', MaintenancePage::class)->name('maintenance.index');
        Route::get('/security-logs', SecurityLogsPage::class)->name('security.logs');
        Route::get('/tickets', TicketsPage::class)->name('tickets.index');
        Route::get('/notifications', BroadcastPage::class)->name('notifications.index');
        Route::get('/settings', SettingsPage::class)->name('settings.index');
    });
});
