<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\CarWashCenter;
use App\Models\GasStation;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\Refuel;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->user_role === 'admin', 403, 'Requires Admin role');
    }

    public function systemOverview(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $monthStart = now()->startOfMonth();

        return response()->json([
            'timestamp' => now(),
            'users' => [
                'total' => User::query()->count(),
                'active' => User::query()->where('is_active', true)->count(),
                'by_role' => User::query()
                    ->selectRaw('user_role, COUNT(*) as count')
                    ->groupBy('user_role')
                    ->pluck('count', 'user_role'),
            ],
            'businesses' => [
                'stations' => [
                    'total' => GasStation::query()->count(),
                    'active' => GasStation::query()->where('is_active', true)->count(),
                ],
                'car_washes' => [
                    'total' => CarWashCenter::query()->count(),
                ],
                'maintenance_centers' => [
                    'total' => MaintenanceCenter::query()->count(),
                ],
            ],
            'subscriptions' => [
                'total' => Subscription::query()->count(),
                'active' => Subscription::query()->where('status', 'active')->where('end_date', '>=', now())->count(),
                'monthly_new' => Subscription::query()->where('created_at', '>=', $monthStart)->count(),
            ],
            'services' => [
                'refuels' => [
                    'total' => Refuel::query()->count(),
                    'monthly' => Refuel::query()->where('refuel_date', '>=', $monthStart)->count(),
                ],
                'car_washes' => [
                    'total' => CarWash::query()->count(),
                    'monthly' => CarWash::query()->where('wash_date', '>=', $monthStart)->count(),
                ],
                'maintenance' => [
                    'total' => MaintenanceService::query()->count(),
                    'monthly' => MaintenanceService::query()->where('service_date', '>=', $monthStart)->count(),
                ],
            ],
        ]);
    }

    public function userGrowth(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        [$period, $start, $buckets, $formatter] = $this->buildBuckets(
            $request->input('period', 'monthly'),
            (int) $request->input('months', 12)
        );

        $users = User::query()->where('created_at', '>=', $start)->get(['created_at', 'is_active']);

        foreach ($users as $user) {
            $label = $formatter(Carbon::parse($user->created_at));
            if (! isset($buckets[$label])) {
                continue;
            }

            $buckets[$label]['new_users']++;
            if ($user->is_active) {
                $buckets[$label]['active_users']++;
            }
        }

        $cumulative = 0;
        $data = collect($buckets)->map(function (array $bucket, string $label) use (&$cumulative) {
            $cumulative += $bucket['new_users'];

            return [
                'period' => $label,
                'new_users' => $bucket['new_users'],
                'active_users' => $bucket['active_users'],
                'cumulative_users' => $cumulative,
            ];
        })->values();

        $first = $data->first();
        $last = $data->last();

        return response()->json([
            'period' => $period,
            'data' => $data,
            'summary' => [
                'total_new_users' => $data->sum('new_users'),
                'total_active_users' => $data->sum('active_users'),
                'growth_rate' => $first && $last && $first['new_users'] > 0
                    ? (($last['cumulative_users'] - $first['new_users']) / $first['new_users']) * 100
                    : 0,
            ],
        ]);
    }

    public function revenueTrends(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        [$period, $start, $buckets, $formatter] = $this->buildBuckets(
            $request->input('period', 'monthly'),
            (int) $request->input('months', 12)
        );

        $refuels = Refuel::query()->where('refuel_date', '>=', $start)->get();

        foreach ($refuels as $refuel) {
            $label = $formatter(Carbon::parse($refuel->refuel_date));
            if (! isset($buckets[$label])) {
                continue;
            }

            $buckets[$label]['total_transactions']++;
            $buckets[$label]['revenue'] += (float) $refuel->final_price;
            $buckets[$label]['total_discount'] += (float) $refuel->discount_amount;
            $buckets[$label]['total_liters'] += (float) $refuel->liters;
        }

        $cumulativeRevenue = 0.0;
        $data = collect($buckets)->map(function (array $bucket, string $label) use (&$cumulativeRevenue) {
            $cumulativeRevenue += $bucket['revenue'];

            return [
                'period' => $label,
                'total_transactions' => $bucket['total_transactions'],
                'revenue' => $bucket['revenue'],
                'total_discount' => $bucket['total_discount'],
                'total_liters' => $bucket['total_liters'],
                'cumulative_revenue' => $cumulativeRevenue,
                'avg_transaction' => $bucket['total_transactions'] > 0 ? $bucket['revenue'] / $bucket['total_transactions'] : 0,
                'avg_price_per_liter' => $bucket['total_liters'] > 0 ? $bucket['revenue'] / $bucket['total_liters'] : 0,
            ];
        })->values();

        $first = $data->first();
        $last = $data->last();

        return response()->json([
            'period' => $period,
            'data' => $data,
            'summary' => [
                'total_revenue' => $data->sum('revenue'),
                'total_transactions' => $data->sum('total_transactions'),
                'total_discount' => $data->sum('total_discount'),
                'total_liters' => $data->sum('total_liters'),
                'avg_transaction' => $data->sum('total_transactions') > 0 ? $data->sum('revenue') / $data->sum('total_transactions') : 0,
                'revenue_growth' => $first && $last && $first['revenue'] > 0
                    ? (($last['revenue'] - $first['revenue']) / $first['revenue']) * 100
                    : 0,
            ],
        ]);
    }

    public function subscriptionAnalytics(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $subscriptionsByPlan = Subscription::query()
            ->selectRaw('plan_type, COUNT(*) as count')
            ->groupBy('plan_type')
            ->pluck('count', 'plan_type');

        $activeSubscriptions = Subscription::query()->where('status', 'active')->where('end_date', '>=', now())->count();
        $inactiveSubscriptions = Subscription::query()->count() - $activeSubscriptions;

        return response()->json([
            'timestamp' => now(),
            'subscriptions_by_plan' => $subscriptionsByPlan,
            'status' => [
                'active' => $activeSubscriptions,
                'inactive' => $inactiveSubscriptions,
            ],
            'summary' => [
                'total_subscriptions' => Subscription::query()->count(),
                'monthly_new_subscriptions' => Subscription::query()->where('created_at', '>=', now()->startOfMonth())->count(),
                'expiring_soon' => Subscription::query()->where('status', 'active')->whereBetween('end_date', [now(), now()->copy()->addDays(7)])->count(),
                'total_revenue' => (float) Subscription::query()->sum('price'),
            ],
        ]);
    }

    private function buildBuckets(string $period, int $months): array
    {
        $period = in_array($period, ['daily', 'weekly', 'monthly'], true) ? $period : 'monthly';

        if ($period === 'daily') {
            $start = now()->subDays(29)->startOfDay();
            $buckets = [];
            for ($date = $start->copy(); $date <= now(); $date->addDay()) {
                $buckets[$date->format('Y-m-d')] = ['new_users' => 0, 'active_users' => 0, 'total_transactions' => 0, 'revenue' => 0.0, 'total_discount' => 0.0, 'total_liters' => 0.0];
            }

            return [$period, $start, $buckets, fn (Carbon $date) => $date->format('Y-m-d')];
        }

        if ($period === 'weekly') {
            $start = now()->subWeeks(11)->startOfWeek();
            $buckets = [];
            for ($date = $start->copy(); $date <= now(); $date->addWeek()) {
                $buckets[$date->format('o-\\WW')] = ['new_users' => 0, 'active_users' => 0, 'total_transactions' => 0, 'revenue' => 0.0, 'total_discount' => 0.0, 'total_liters' => 0.0];
            }

            return [$period, $start, $buckets, fn (Carbon $date) => $date->copy()->startOfWeek()->format('o-\\WW')];
        }

        $start = now()->subMonths(max(1, $months) - 1)->startOfMonth();
        $buckets = [];
        for ($date = $start->copy(); $date <= now(); $date->addMonth()) {
            $buckets[$date->format('Y-m')] = ['new_users' => 0, 'active_users' => 0, 'total_transactions' => 0, 'revenue' => 0.0, 'total_discount' => 0.0, 'total_liters' => 0.0];
        }

        return [$period, $start, $buckets, fn (Carbon $date) => $date->format('Y-m')];
    }
}
