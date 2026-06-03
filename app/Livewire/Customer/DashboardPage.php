<?php

namespace App\Livewire\Customer;

use App\Models\Refuel;
use App\Models\Subscription;
use App\Models\ActivityLog;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.customer')]
#[Title('الرئيسية - غازي')]
class DashboardPage extends Component
{
    public function render()
    {
        $user = Auth::user();

        $activeSub = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest('id')->first();

        $totalRefuels = Refuel::where('user_id', $user->id)->count();
        $totalLiters  = Refuel::where('user_id', $user->id)->sum('liters');

        $thisMonthRefuels = Refuel::where('user_id', $user->id)
            ->whereMonth('refuel_date', now()->month)
            ->whereYear('refuel_date', now()->year)
            ->count();
        $thisMonthLiters = Refuel::where('user_id', $user->id)
            ->whereMonth('refuel_date', now()->month)
            ->whereYear('refuel_date', now()->year)
            ->sum('liters');

        $recentLogs = ActivityLog::where('user_id', $user->id)
            ->latest()->limit(5)->get();

        $ads = Advertisement::active()->get();

        return view('livewire.customer.dashboard-page', compact(
            'user', 'activeSub', 'totalRefuels', 'totalLiters',
            'thisMonthRefuels', 'thisMonthLiters', 'recentLogs', 'ads'
        ))->layoutData(['showNav' => true]);
    }
}
