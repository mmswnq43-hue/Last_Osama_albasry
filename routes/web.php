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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// ═══════════════════════════════════════════════
// Emergency admin setup — ONE TIME USE ONLY
// Visit: /ghazi-admin-setup?token=Ghazi@Setup2026
// ═══════════════════════════════════════════════
Route::get('/ghazi-admin-setup', function (\Illuminate\Http\Request $req) {
    if ($req->query('token') !== 'Ghazi@Setup2026') {
        abort(404);
    }

    try {
        // Run pending migrations first
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        // Find or create admin
        $admin = DB::table('users')->where('user_role', 'admin')->first();

        if ($admin) {
            DB::table('users')->where('id', $admin->id)->update([
                'phone'           => '770794503',
                'email'           => 'm.mosonaq@gmail.com',
                'password_hash'   => Hash::make('770794503'),
                'full_name'       => 'Admin',
                'is_active'       => 1,
                'approval_status' => 'approved',
            ]);
            $msg = "✅ تم تحديث الأدمن الموجود (ID: {$admin->id})";
        } else {
            DB::table('users')->insert([
                'full_name'       => 'Admin',
                'phone'           => '770794503',
                'email'           => 'm.mosonaq@gmail.com',
                'password_hash'   => Hash::make('770794503'),
                'user_role'       => 'admin',
                'qr_code'         => 'QR-ADMIN-' . strtoupper(Str::random(8)),
                'is_active'       => 1,
                'phone_verified'  => 1,
                'approval_status' => 'approved',
            ]);
            $msg = "✅ تم إنشاء الأدمن بنجاح";
        }

        // Verify
        $check = DB::table('users')->where('email', 'm.mosonaq@gmail.com')->first();
        $passOk = $check && Hash::check('770794503', $check->password_hash);

        return response("<html dir='rtl'><head><meta charset='UTF-8'><title>Admin Setup</title>
            <style>body{font-family:Arial;background:#0f172a;color:white;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}
            .card{background:#1e293b;padding:32px;border-radius:16px;max-width:500px;width:90%;}
            h2{color:#f97316;} pre{background:#0f172a;padding:12px;border-radius:8px;font-size:12px;overflow:auto;}
            .ok{color:#4ade80;} .err{color:#f87171;}
            a{display:block;margin-top:20px;background:linear-gradient(135deg,#f97316,#1d4ed8);color:white;padding:12px;border-radius:8px;text-align:center;text-decoration:none;}</style>
        </head><body><div class='card'>
            <h2>⚙️ Admin Setup — غازي</h2>
            <p class='" . ($passOk ? 'ok' : 'err') . "'>$msg</p>
            <p>البريد: <b>m.mosonaq@gmail.com</b></p>
            <p>كلمة المرور: <b>770794503</b></p>
            <p>التحقق من كلمة المرور: <span class='" . ($passOk ? 'ok' : 'err') . "'>" . ($passOk ? '✅ صحيحة' : '❌ خطأ') . "</span></p>
            <details><summary>Migrations Output</summary><pre>" . htmlspecialchars($migrateOutput) . "</pre></details>
            <a href='/admin/login'>الذهاب لصفحة تسجيل الدخول ←</a>
        </div></body></html>", 200, ['Content-Type' => 'text/html; charset=utf-8']);

    } catch (\Exception $e) {
        return response("<html><body style='background:#0f172a;color:#f87171;font-family:monospace;padding:20px;'>
            <h2>Error</h2><pre>" . htmlspecialchars($e->getMessage()) . "</pre>
            <pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>
        </body></html>", 500, ['Content-Type' => 'text/html']);
    }
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
