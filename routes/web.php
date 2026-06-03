<?php

use App\Livewire\Admin\Auth\LoginPage;
use App\Livewire\Admin\Auth\ForgotPasswordPage;
use App\Livewire\Admin\Auth\ResetPasswordPage;
use App\Livewire\Admin\DashboardPage;
use App\Livewire\Admin\Users\PendingApprovalsPage;
use App\Livewire\Admin\Users\UsersListPage;
use App\Livewire\Admin\Users\OwnersListPage;
use App\Livewire\Admin\Subscriptions\SubscriptionsListPage;
use App\Livewire\Admin\Businesses\StationsPage;
use App\Livewire\Admin\Businesses\CarWashesPage;
use App\Livewire\Admin\Businesses\MaintenancePage;
use App\Livewire\Admin\Security\SecurityLogsPage;
use App\Livewire\Admin\Tickets\TicketsPage;
use App\Livewire\Admin\Notifications\BroadcastPage;
use App\Livewire\Admin\Settings\SettingsPage;
use App\Livewire\Admin\BankAccounts\BankAccountsPage;
use App\Livewire\Customer\RegisterPage;
use App\Livewire\Customer\StatusPage;
use App\Livewire\Customer\DashboardPage as CustomerDashboardPage;
use App\Livewire\Customer\SubscriptionsPage;
use App\Livewire\Customer\MapPage;
use App\Livewire\Customer\NotificationsPage;
use App\Livewire\Customer\SettingsPage as CustomerSettingsPage;
use App\Livewire\Customer\HistoryPage;
use App\Http\Controllers\Customer\RegistrationController;
use App\Http\Controllers\Customer\SubscriptionController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Livewire\Admin\Advertisements\AdvertisementsPage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// ── Unified Auth (guest only) ──────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',           LoginPage::class)->name('login');
    Route::get('/forgot-password', ForgotPasswordPage::class)->name('password.forgot');
    Route::get('/reset-password',  ResetPasswordPage::class)->name('password.reset');
    Route::get('/register',                    RegisterPage::class)->name('customer.register');
    Route::post('/complete-registration', [RegistrationController::class, 'store'])->name('customer.complete-registration');
});

// ── Unified Logout ─────────────────────────────────────────────
Route::post('/logout', function () {
    $role = auth()->user()?->user_role;
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// ── Admin protected pages ──────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard',     DashboardPage::class)->name('dashboard');
    Route::get('/users/pending', PendingApprovalsPage::class)->name('users.pending');
    Route::get('/users',         UsersListPage::class)->name('users.index');
    Route::get('/owners',        OwnersListPage::class)->name('owners.index');
    Route::get('/subscriptions', SubscriptionsListPage::class)->name('subscriptions.index');
    Route::get('/stations',      StationsPage::class)->name('stations.index');
    Route::get('/car-washes',    CarWashesPage::class)->name('carwashes.index');
    Route::get('/maintenance',   MaintenancePage::class)->name('maintenance.index');
    Route::get('/security-logs', SecurityLogsPage::class)->name('security.logs');
    Route::get('/tickets',       TicketsPage::class)->name('tickets.index');
    Route::get('/notifications', BroadcastPage::class)->name('notifications.index');
    Route::get('/settings',      SettingsPage::class)->name('settings.index');
    Route::get('/bank-accounts', BankAccountsPage::class)->name('bankaccounts.index');

    // ── Advertisements ────────────────────────────────────────
    Route::get('/ads',           AdvertisementsPage::class)->name('ads.index');
    Route::get('/ads/create',    fn () => view('admin.advertisements.form', [
        'title' => 'إضافة إعلان جديد',
        'action' => route('admin.ads.store'),
        'method' => null,
    ]))->name('ads.create');
    Route::post('/ads',          [AdvertisementController::class, 'store'])->name('ads.store');
    Route::get('/ads/{ad}/edit', fn (\App\Models\Advertisement $ad) => view('admin.advertisements.form', [
        'title'  => 'تعديل إعلان',
        'action' => route('admin.ads.update', $ad->id),
        'method' => 'PUT',
        'ad'     => $ad,
    ]))->name('ads.edit');
    Route::put('/ads/{ad}',      [AdvertisementController::class, 'update'])->name('ads.update');
});

// ── Customer protected pages ───────────────────────────────────
Route::prefix('customer')->name('customer.')->middleware('auth')->group(function () {
    Route::get('/status',        StatusPage::class)->name('status');
    Route::get('/dashboard',     CustomerDashboardPage::class)->name('dashboard');
    Route::get('/subscriptions', SubscriptionsPage::class)->name('subscriptions');
    Route::get('/map',           MapPage::class)->name('map');
    Route::get('/notifications', NotificationsPage::class)->name('notifications');
    Route::get('/settings',      CustomerSettingsPage::class)->name('settings');
    Route::get('/history',       HistoryPage::class)->name('history');
    Route::get('/pay',           [SubscriptionController::class, 'showPay'])->name('pay');
    Route::post('/subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
});
