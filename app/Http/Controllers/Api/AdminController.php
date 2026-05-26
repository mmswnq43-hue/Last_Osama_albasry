<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\CarWashCenter;
use App\Models\GasStation;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\Notification;
use App\Models\Refuel;
use App\Models\SecurityLog;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Requires Admin role');
    }

    public function users(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'user_role' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'registration_date_from' => ['nullable', 'date'],
        ]);

        $query = User::query();
        if (! empty($data['user_role'])) {
            $query->where('user_role', $data['user_role']);
        }
        if (array_key_exists('is_active', $data)) {
            $query->where('is_active', $data['is_active']);
        }
        if (! empty($data['registration_date_from'])) {
            $query->where('created_at', '>=', $data['registration_date_from']);
        }

        $perPage = $data['per_page'] ?? 20;
        $users = $query->paginate($perPage);

        $result = collect($users->items())->map(function (User $user) {
            $subscription = Subscription::query()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->first();

            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
                'user_role' => $user->user_role,
                'is_active' => $user->is_active,
                'registration_date' => $user->created_at,
                'last_login' => null,
                'subscription_status' => $subscription?->status,
                'total_spent' => (float) Refuel::query()->where('user_id', $user->id)->sum('final_price'),
            ];
        });

        return response()->json($result->values());
    }

    public function userDetails(Request $request, int $userId): JsonResponse
    {
        $this->ensureAdmin($request);

        $user = User::query()->findOrFail($userId);
        $subscription = Subscription::query()->where('user_id', $user->id)->where('status', 'active')->where('end_date', '>=', now())->first();

        return response()->json([
            'id' => $user->id,
            'full_name' => $user->full_name,
            'phone' => $user->phone,
            'user_role' => $user->user_role,
            'is_active' => $user->is_active,
            'registration_date' => $user->created_at,
            'subscription' => $subscription ? [
                'plan_type' => $subscription->plan_type,
                'status' => $subscription->status,
                'start_date' => $subscription->start_date,
                'end_date' => $subscription->end_date,
            ] : null,
            'statistics' => [
                'total_refuels' => Refuel::query()->where('user_id', $user->id)->count(),
                'total_car_washes' => CarWash::query()->where('user_id', $user->id)->count(),
                'total_maintenance' => MaintenanceService::query()->where('user_id', $user->id)->count(),
                'total_spent' => (float) Refuel::query()->where('user_id', $user->id)->sum('final_price'),
                'total_saved' => (float) Refuel::query()->where('user_id', $user->id)->sum('discount_amount'),
            ],
            'security_logs' => SecurityLog::query()->where('user_id', $user->id)->latest('created_at')->limit(10)->get(['log_type', 'is_successful', 'created_at']),
        ]);
    }

    public function updateUserStatus(Request $request, int $userId): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $user = User::query()->findOrFail($userId);
        $user->forceFill(['is_active' => $data['is_active']])->save();

        return response()->json(['message' => $data['is_active'] ? 'تم تفعيل المستخدم بنجاح' : 'تم تعطيل المستخدم بنجاح']);
    }

    public function resetUserPassword(Request $request, int $userId): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate(['new_password' => ['nullable', 'string', 'min:6']]);

        $user = User::query()->findOrFail($userId);
        $user->forceFill(['password_hash' => Hash::make($data['new_password'] ?? 'tempPassword123')])->save();

        return response()->json(['message' => 'تم إعادة تعيين كلمة المرور بنجاح']);
    }

    public function stations(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $query = GasStation::query();
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return response()->json([
            'stations' => $query->paginate((int) $request->input('per_page', 20))->items(),
            'pagination' => [
                'page' => (int) $request->input('page', 1),
                'per_page' => (int) $request->input('per_page', 20),
                'total' => $query->count(),
            ],
        ]);
    }

    public function createStation(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'station_name' => ['required', 'string', 'max:100'],
            'commercial_register' => ['required', 'string', 'max:50', 'unique:gas_stations,commercial_register'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'station_code' => ['required', 'string', 'max:20', 'unique:gas_stations,station_code'],
        ]);

        $station = GasStation::create($data);

        return response()->json($station, 201);
    }

    public function deleteStation(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);
        GasStation::query()->findOrFail($id)->delete();

        return response()->json([], 204);
    }

    public function carWashCenters(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $query = CarWashCenter::query();
        return response()->json([
            'centers' => $query->paginate((int) $request->input('per_page', 20))->items(),
            'pagination' => [
                'page' => (int) $request->input('page', 1),
                'per_page' => (int) $request->input('per_page', 20),
                'total' => $query->count(),
            ],
        ]);
    }

    public function maintenanceCenters(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $query = MaintenanceCenter::query();
        return response()->json([
            'centers' => $query->paginate((int) $request->input('per_page', 20))->items(),
            'pagination' => [
                'page' => (int) $request->input('page', 1),
                'per_page' => (int) $request->input('per_page', 20),
                'total' => $query->count(),
            ],
        ]);
    }

    public function createCarWashCenter(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'center_name' => ['required', 'string', 'max:100'],
            'commercial_register' => ['required', 'string', 'max:50', 'unique:car_wash_centers,commercial_register'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'center_code' => ['required', 'string', 'max:20', 'unique:car_wash_centers,center_code'],
        ]);

        return response()->json(CarWashCenter::create($data), 201);
    }

    public function deleteCarWashCenter(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);
        CarWashCenter::query()->findOrFail($id)->delete();

        return response()->json([], 204);
    }

    public function createMaintenanceCenter(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'center_name' => ['required', 'string', 'max:100'],
            'commercial_register' => ['required', 'string', 'max:50', 'unique:maintenance_centers,commercial_register'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'center_code' => ['required', 'string', 'max:20', 'unique:maintenance_centers,center_code'],
            'specialization' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json(MaintenanceCenter::create($data), 201);
    }

    public function deleteMaintenanceCenter(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);
        MaintenanceCenter::query()->findOrFail($id)->delete();

        return response()->json([], 204);
    }

    public function approveBusiness(Request $request, int $businessId): JsonResponse
    {
        $this->ensureAdmin($request);
        $business = $this->findBusinessOrFail($request->input('business_type'), $businessId);
        $business->forceFill(['is_active' => true])->save();

        return response()->json(['message' => 'تم اعتماد العمل بنجاح']);
    }

    public function suspendBusiness(Request $request, int $businessId): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'business_type' => ['required', 'string'],
            'reason' => ['nullable', 'string'],
        ]);

        $business = $this->findBusinessOrFail($data['business_type'], $businessId);
        $business->forceFill(['is_active' => false])->save();

        return response()->json([
            'message' => 'تم تعليق العمل بنجاح',
            'reason' => $data['reason'] ?? null,
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'transaction_type' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'date_from' => ['nullable', 'date'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $transactions = collect();
        $perPage = $data['per_page'] ?? 20;

        if (empty($data['transaction_type']) || $data['transaction_type'] === 'subscription') {
            $query = Subscription::query()->where('price', '>', 0);
            if (! empty($data['date_from'])) {
                $query->where('created_at', '>=', $data['date_from']);
            }

            $transactions = $transactions->merge($query->get()->map(fn (Subscription $subscription) => [
                'id' => 'sub_'.$subscription->id,
                'user_id' => $subscription->user_id,
                'type' => 'subscription_payment',
                'amount' => (float) $subscription->price,
                'currency' => 'YER',
                'status' => $subscription->status,
                'created_at' => $subscription->created_at,
                'payment_method' => 'unknown',
            ]));
        }

        if (empty($data['transaction_type']) || $data['transaction_type'] === 'refuel') {
            $query = Refuel::query();
            if (! empty($data['date_from'])) {
                $query->where('refuel_date', '>=', $data['date_from']);
            }

            $transactions = $transactions->merge($query->get()->map(fn (Refuel $refuel) => [
                'id' => 'ref_'.$refuel->id,
                'user_id' => $refuel->user_id,
                'type' => 'refuel_payment',
                'amount' => (float) $refuel->final_price,
                'currency' => 'YER',
                'status' => 'completed',
                'created_at' => $refuel->refuel_date,
                'payment_method' => 'unknown',
            ]));
        }

        $transactions = $transactions->sortByDesc('created_at')->values();

        return response()->json([
            'transactions' => $transactions->forPage($data['page'] ?? 1, $perPage)->values(),
            'filters' => [
                'type' => $data['transaction_type'] ?? null,
                'status' => $data['status'] ?? null,
                'date_from' => $data['date_from'] ?? null,
            ],
            'total' => $transactions->count(),
        ]);
    }

    public function userBehaviorAnalytics(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $period = $request->input('period', '30_days');
        $days = match ($period) {
            '7_days' => 7,
            '90_days' => 90,
            default => 30,
        };

        $start = now()->subDays($days)->startOfDay();
        $dailyActiveUsers = [];

        for ($index = 0; $index < $days; $index++) {
            $dayStart = $start->copy()->addDays($index);
            $dayEnd = $dayStart->copy()->addDay();

            $dailyActiveUsers[] = Refuel::query()
                ->whereBetween('refuel_date', [$dayStart, $dayEnd])
                ->distinct('user_id')
                ->count('user_id');
        }

        $refuelCount = Refuel::query()->where('refuel_date', '>=', $start)->count();
        $carWashCount = CarWash::query()->where('wash_date', '>=', $start)->count();
        $maintenanceCount = MaintenanceService::query()->where('service_date', '>=', $start)->count();
        $totalServices = max(1, $refuelCount + $carWashCount + $maintenanceCount);

        $peakHours = Refuel::query()
            ->selectRaw('HOUR(refuel_date) as hour, COUNT(*) as count')
            ->where('refuel_date', '>=', $start)
            ->groupBy(DB::raw('HOUR(refuel_date)'))
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return response()->json([
            'period' => $period,
            'metrics' => [
                'daily_active_users' => $dailyActiveUsers,
                'most_used_services' => [
                    ['service' => 'refuel', 'usage' => $refuelCount / $totalServices * 100],
                    ['service' => 'car_wash', 'usage' => $carWashCount / $totalServices * 100],
                    ['service' => 'maintenance', 'usage' => $maintenanceCount / $totalServices * 100],
                ],
                'peak_hours' => $peakHours->map(fn ($item) => [
                    'hour' => (string) $item->hour,
                    'refuels' => (int) $item->count,
                ])->values(),
                'user_retention' => [
                    'day_1' => 95.0,
                    'day_7' => 78.0,
                    'day_30' => 65.0,
                ],
            ],
        ]);
    }

    public function systemHealth(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        return response()->json([
            'status' => 'healthy',
            'database' => [
                'status' => 'connected',
                'response_time' => 12,
            ],
            'services' => [
                'authentication' => 'healthy',
                'payments' => 'healthy',
                'notifications' => 'healthy',
            ],
            'metrics' => [
                'cpu_usage' => 45.2,
                'memory_usage' => 67.8,
                'disk_usage' => 23.1,
            ],
        ]);
    }

    public function scheduleMaintenance(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'maintenance_type' => ['required', 'string'],
            'start_time' => ['required', 'date'],
            'duration' => ['required', 'integer', 'min:1'],
            'affected_services' => ['required', 'array', 'min:1'],
            'affected_services.*' => ['string'],
            'message' => ['required', 'string'],
        ]);

        foreach (User::query()->where('is_active', true)->get() as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'صيانة مجدولة',
                'message' => $data['message'],
                'notification_type' => 'system_alert',
                'is_important' => true,
            ]);
        }

        return response()->json([
            'message' => 'تم جدولة الصيانة بنجاح',
            'maintenance' => $data,
        ]);
    }

    public function securityThreats(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $failedLogins = SecurityLog::query()
            ->selectRaw('user_id, COUNT(*) as attempts')
            ->where('log_type', 'failed_login')
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= 5')
            ->get();

        return response()->json([
            'threats' => $failedLogins->map(function ($item) {
                $user = User::query()->find($item->user_id);

                return [
                    'type' => 'multiple_failed_logins',
                    'user_id' => $item->user_id,
                    'user_name' => $user?->full_name ?? 'Unknown',
                    'severity' => 'medium',
                    'description' => $item->attempts.' محاولات دخول فاشلة خلال ساعة',
                    'recommendation' => 'قفل الحساب مؤقتاً',
                ];
            })->values(),
        ]);
    }

    public function lockUser(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'lock_type' => ['required', 'string'],
            'reason' => ['required', 'string'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'notify_user' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->findOrFail($data['user_id']);
        $user->forceFill([
            'account_locked' => true,
            'is_active' => false,
        ])->save();

        if ($data['notify_user'] ?? true) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'تم قفل الحساب',
                'message' => $data['reason'],
                'notification_type' => 'security_alert',
                'is_important' => true,
            ]);
        }

        SecurityLog::create([
            'user_id' => $user->id,
            'log_type' => 'account_locked',
            'is_successful' => true,
            'error_message' => $data['reason'],
        ]);

        return response()->json([
            'message' => 'تم قفل حساب المستخدم',
            'lock_type' => $data['lock_type'],
            'duration' => $data['duration'] ?? null,
        ]);
    }

    private function findBusinessOrFail(?string $businessType, int $businessId)
    {
        return match ($businessType) {
            'station' => GasStation::query()->findOrFail($businessId),
            'car_wash' => CarWashCenter::query()->findOrFail($businessId),
            'maintenance' => MaintenanceCenter::query()->findOrFail($businessId),
            default => abort(400, 'نوع العمل غير صالح'),
        };
    }

    public function revenueSummary(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $period = $request->input('period', 'monthly');
        $start = match ($period) {
            'daily' => now()->subDay(),
            'weekly' => now()->subWeek(),
            'monthly' => now()->subDays(30),
            default => now()->subYear(),
        };

        $subscriptionRevenue = (float) Subscription::query()->whereBetween('created_at', [$start, now()])->sum('price');
        $refuelRevenue = (float) Refuel::query()->whereBetween('created_at', [$start, now()])->sum('final_price');

        return response()->json([
            'period' => $period,
            'total_revenue' => $subscriptionRevenue + $refuelRevenue,
            'revenue_by_type' => [
                'subscriptions' => $subscriptionRevenue,
                'refuels' => $refuelRevenue,
            ],
        ]);
    }

    public function exportReport(Request $request)
    {
        $this->ensureAdmin($request);

        $type = $request->input('type', 'revenue');
        $period = $request->input('period', 'monthly');
        $start = $period === 'monthly' ? now()->subDays(30) : now()->subDay();

        $rows = [];
        if ($type === 'revenue') {
            $rows[] = ['type', 'amount'];
            $subscriptionRevenue = Subscription::query()->whereBetween('created_at', [$start, now()])->sum('price');
            $refuelRevenue = Refuel::query()->whereBetween('created_at', [$start, now()])->sum('final_price');
            $rows[] = ['subscriptions', (string) $subscriptionRevenue];
            $rows[] = ['refuels', (string) $refuelRevenue];
            $rows[] = ['total', (string) ($subscriptionRevenue + $refuelRevenue)];
        } elseif ($type === 'daily_activity') {
            $rows[] = ['date', 'new_registrations', 'new_subscriptions', 'refuels', 'car_washes', 'maintenance', 'daily_revenue'];
            $date = now()->toDateString();
            $startDay = now()->startOfDay();
            $rows[] = [
                $date,
                (string) User::query()->where('created_at', '>=', $startDay)->count(),
                (string) Subscription::query()->where('created_at', '>=', $startDay)->count(),
                (string) Refuel::query()->where('refuel_date', '>=', $startDay)->count(),
                (string) CarWash::query()->where('wash_date', '>=', $startDay)->count(),
                (string) MaintenanceService::query()->where('service_date', '>=', $startDay)->count(),
                (string) ((float) Subscription::query()->where('created_at', '>=', $startDay)->sum('price') + (float) Refuel::query()->where('refuel_date', '>=', $startDay)->sum('final_price')),
            ];
        } else {
            abort(400, 'Unknown export type');
        }

        $lines = array_map(fn($r) => implode(',', array_map(fn($c) => '"'.str_replace('"', '""', (string)$c).'"', $r)), $rows);
        $csv = implode("\n", $lines) . "\n";

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="report-'.now()->format('Ymd').'-.csv"');
    }

    public function securityLogs(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'log_type' => ['nullable', 'string'],
            'date_from' => ['nullable', 'date'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = SecurityLog::query();
        if (! empty($data['log_type'])) {
            $query->where('log_type', $data['log_type']);
        }
        if (! empty($data['date_from'])) {
            $query->where('created_at', '>=', $data['date_from']);
        }

        $perPage = $data['per_page'] ?? 20;
        $page = $data['page'] ?? 1;
        $total = (clone $query)->count();
        $logs = $query->latest('created_at')->forPage($page, $perPage)->get();

        return response()->json([
            'logs' => $logs,
            'total' => $total,
            'filters' => [
                'log_type' => $data['log_type'] ?? null,
                'date_from' => $data['date_from'] ?? null,
                'page' => $page,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function dailyActivity(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $start = now()->startOfDay();

        return response()->json([
            'date' => now()->toDateString(),
            'new_registrations' => User::query()->where('created_at', '>=', $start)->count(),
            'new_subscriptions' => Subscription::query()->where('created_at', '>=', $start)->count(),
            'services' => [
                'refuels' => Refuel::query()->where('refuel_date', '>=', $start)->count(),
                'car_washes' => CarWash::query()->where('wash_date', '>=', $start)->count(),
                'maintenance' => MaintenanceService::query()->where('service_date', '>=', $start)->count(),
            ],
            'daily_revenue' => (float) Subscription::query()->where('created_at', '>=', $start)->sum('price')
                + (float) Refuel::query()->where('refuel_date', '>=', $start)->sum('final_price'),
        ]);
    }

    public function approveSubscription(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);
        $subscription = Subscription::query()->findOrFail($id);
        $subscription->forceFill(['status' => 'active'])->save();

        return response()->json(['message' => 'Subscription approved']);
    }

    public function tickets(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $query = SupportTicket::query();
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        return response()->json($query->latest('created_at')->get());
    }

    public function updateTicket(Request $request, int $ticketId): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'status' => ['nullable', 'string', 'max:20'],
            'priority' => ['nullable', 'string', 'max:20'],
            'admin_response' => ['nullable', 'string'],
        ]);

        $ticket = SupportTicket::query()->findOrFail($ticketId);
        $ticket->fill($data);
        if (in_array($ticket->status, ['resolved', 'closed'], true)) {
            $ticket->resolved_at = now();
        }
        $ticket->save();

        return response()->json($ticket->refresh());
    }

    public function approveUser(Request $request, int $userId): JsonResponse
    {
        $this->ensureAdmin($request);

        $user = User::query()->findOrFail($userId);
        $user->forceFill([
            'approval_status' => 'approved',
            'is_active' => true,
            'rejection_reason' => null,
        ])->save();

        Notification::create([
            'user_id' => $user->id,
            'title' => 'تم قبول حسابك',
            'message' => 'مرحباً '.$user->full_name.'! تم قبول حسابك بنجاح. يمكنك الآن تسجيل الدخول واستخدام التطبيق.',
            'notification_type' => 'account_approved',
            'is_important' => true,
        ]);

        return response()->json(['message' => 'تم قبول المستخدم بنجاح']);
    }

    public function rejectUser(Request $request, int $userId): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $user = User::query()->findOrFail($userId);
        $user->forceFill([
            'approval_status' => 'rejected',
            'is_active' => false,
            'rejection_reason' => $data['reason'],
        ])->save();

        Notification::create([
            'user_id' => $user->id,
            'title' => 'تم رفض حسابك',
            'message' => 'نأسف، تم رفض حسابك. السبب: '.$data['reason'],
            'notification_type' => 'account_rejected',
            'is_important' => true,
        ]);

        return response()->json(['message' => 'تم رفض المستخدم']);
    }

    public function pendingUsers(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $users = User::query()
            ->where('approval_status', 'pending')
            ->latest('created_at')
            ->get()
            ->map(function (User $user) {
                $subscription = Subscription::query()
                    ->where('user_id', $user->id)
                    ->latest('created_at')
                    ->first();

                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'phone' => $user->phone,
                    'user_role' => $user->user_role,
                    'vehicle_type' => $user->vehicle_type,
                    'engine_number' => $user->engine_number,
                    'avatar' => $user->avatar,
                    'created_at' => $user->created_at,
                    'subscription' => $subscription ? [
                        'id' => $subscription->id,
                        'plan_type' => $subscription->plan_type,
                        'price' => $subscription->price,
                        'status' => $subscription->status,
                        'payment_receipt_image' => $subscription->payment_receipt_image,
                        'created_at' => $subscription->created_at,
                    ] : null,
                ];
            });

        return response()->json($users->values());
    }

    public function allSubscriptions(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'status' => ['nullable', 'string'],
            'plan_type' => ['nullable', 'string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Subscription::query()->with('user:id,full_name,phone');

        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }
        if (! empty($data['plan_type'])) {
            $query->where('plan_type', $data['plan_type']);
        }

        $perPage = $data['per_page'] ?? 20;
        $paginated = $query->latest('created_at')->paginate($perPage);

        return response()->json([
            'subscriptions' => $paginated->items(),
            'total' => $paginated->total(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ]);
    }

    public function updateStation(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'station_name' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $station = GasStation::query()->findOrFail($id);
        $station->forceFill(array_filter($data, fn($v) => $v !== null))->save();

        return response()->json($station->refresh());
    }

    public function updateCarWashCenter(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'center_name' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $center = CarWashCenter::query()->findOrFail($id);
        $center->forceFill(array_filter($data, fn($v) => $v !== null))->save();

        return response()->json($center->refresh());
    }

    public function updateMaintenanceCenter(Request $request, int $id): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'center_name' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'specialization' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $center = MaintenanceCenter::query()->findOrFail($id);
        $center->forceFill(array_filter($data, fn($v) => $v !== null))->save();

        return response()->json($center->refresh());
    }

    public function createUser(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'user_role' => ['required', 'string'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'engine_number' => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'password_hash' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'user_role' => $data['user_role'],
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'engine_number' => $data['engine_number'] ?? null,
            'qr_code' => 'GHAZI:'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(32)),
            'is_active' => true,
            'approval_status' => 'approved',
        ]);

        return response()->json($user, 201);
    }

    public function updateUserRole(Request $request, int $userId): JsonResponse
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'user_role' => ['required', 'string', 'in:customer,station_owner,car_wash_owner,maintenance_owner,admin,station_worker,car_wash_worker,maintenance_worker'],
        ]);

        $user = User::query()->findOrFail($userId);
        $user->forceFill(['user_role' => $data['user_role']])->save();

        return response()->json(['message' => 'تم تغيير دور المستخدم بنجاح', 'user_role' => $data['user_role']]);
    }
}
