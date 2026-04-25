<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\CarWashCenter;
use App\Models\ElectronicCard;
use App\Models\Employee;
use App\Models\GasStation;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\Refuel;
use App\Models\SecurityLog;
use App\Models\Subscription;
use App\Models\User;
use App\Services\GeoService;
use App\Services\GasYemenSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function __construct(
        private readonly GasYemenSubscriptionService $subscriptions,
        private readonly GeoService $geo,
    ) {
    }

    private function ensureWorker(Request $request): Employee
    {
        abort_unless(in_array($request->user()->user_role, ['station_worker', 'car_wash_worker', 'maintenance_worker', 'admin'], true), 403, 'Requires Worker role');

        $employee = Employee::query()->where('user_id', $request->user()->id)->first();
        abort_if(! $employee && $request->user()->user_role !== 'admin', 404, 'Employee not found');

        return $employee ?? new Employee();
    }

    public function myWorkplace(Request $request): JsonResponse
    {
        $employee = $this->ensureWorker($request);

        $workplace = null;
        $workplaceType = null;

        if ($employee->station_id) {
            $workplace = GasStation::query()->find($employee->station_id);
            $workplaceType = 'gas_station';
        } elseif ($employee->car_wash_center_id) {
            $workplace = CarWashCenter::query()->find($employee->car_wash_center_id);
            $workplaceType = 'car_wash';
        } elseif ($employee->maintenance_center_id) {
            $workplace = MaintenanceCenter::query()->find($employee->maintenance_center_id);
            $workplaceType = 'maintenance';
        }

        return response()->json([
            'employee_code' => $employee->employee_code,
            'position' => $employee->position,
            'workplace_type' => $workplaceType,
            'workplace' => $workplace,
        ]);
    }

    public function myRefuelHistory(Request $request): JsonResponse
    {
        $employee = $this->ensureWorker($request);
        return response()->json(Refuel::query()->where('employee_id', $employee->id)->latest('refuel_date')->get());
    }

    public function myCarWashHistory(Request $request): JsonResponse
    {
        $employee = $this->ensureWorker($request);
        return response()->json(CarWash::query()->where('employee_id', $employee->id)->latest('wash_date')->get());
    }

    public function myMaintenanceHistory(Request $request): JsonResponse
    {
        $employee = $this->ensureWorker($request);
        return response()->json(MaintenanceService::query()->where('employee_id', $employee->id)->latest('service_date')->get());
    }

    public function myStats(Request $request): JsonResponse
    {
        $employee = $this->ensureWorker($request);
        $period = $request->input('period', 'monthly');
        $start = $period === 'monthly' ? now()->subDays(30) : now()->subDay();

        $totalServices = 0;
        $totalVolume = 0;

        if ($employee->station_id) {
            $totalServices = Refuel::query()->where('employee_id', $employee->id)->where('refuel_date', '>=', $start)->count();
            $totalVolume = (float) Refuel::query()->where('employee_id', $employee->id)->where('refuel_date', '>=', $start)->sum('liters');
        } elseif ($employee->car_wash_center_id) {
            $totalServices = CarWash::query()->where('employee_id', $employee->id)->where('wash_date', '>=', $start)->count();
        } elseif ($employee->maintenance_center_id) {
            $totalServices = MaintenanceService::query()->where('employee_id', $employee->id)->where('service_date', '>=', $start)->count();
        }

        return response()->json([
            'period' => $period,
            'total_services' => $totalServices,
            'total_volume_or_revenue' => $totalVolume,
            'employee_code' => $employee->employee_code,
        ]);
    }

    public function validateRefuelQr(Request $request): JsonResponse
    {
        $this->ensureWorker($request);
        $data = $request->validate([
            'qr_code' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'station_id' => ['required', 'integer', 'exists:gas_stations,id'],
            'vehicle_type' => ['nullable', 'string'],
            'engine_number' => ['nullable', 'string'],
            'user_lat' => ['nullable', 'numeric'],
            'user_lon' => ['nullable', 'numeric'],
        ]);

        $user = User::query()->findOrFail($data['user_id']);
        $station = GasStation::query()->where('id', $data['station_id'])->where('is_active', true)->first();
        if (! $station) {
            $this->logSecurityEvent($data['user_id'], 'refuel_qr_validation', false, 'المحطة غير موجودة أو غير نشطة', $request, $data);

            return response()->json(['is_valid' => false, 'error_message' => 'المحطة غير موجودة أو غير نشطة']);
        }

        if ($user->qr_code !== $data['qr_code']) {
            $this->logSecurityEvent($data['user_id'], 'refuel_qr_validation', false, 'QR code mismatch', $request, $data);

            return response()->json(['is_valid' => false, 'error_message' => 'QR code mismatch']);
        }

        $subscription = Subscription::query()->where('user_id', $user->id)->where('status', 'active')->where('end_date', '>=', now())->first();
        if (! $subscription) {
            $this->logSecurityEvent($data['user_id'], 'refuel_qr_validation', false, 'لا يوجد اشتراك نشط', $request, $data);

            return response()->json(['is_valid' => false, 'error_message' => 'لا يوجد اشتراك نشط', 'discount_percent' => null]);
        }

        if (! empty($data['vehicle_type']) && $user->vehicle_type !== $data['vehicle_type']) {
            $this->logSecurityEvent($data['user_id'], 'refuel_qr_validation', false, 'نوع السيارة غير مطابق', $request, $data);

            return response()->json(['is_valid' => false, 'error_message' => 'نوع السيارة غير مطابق', 'discount_percent' => null]);
        }

        if (! empty($data['engine_number']) && $user->engine_number !== $data['engine_number']) {
            $this->logSecurityEvent($data['user_id'], 'refuel_qr_validation', false, 'رقم المحرك غير مطابق', $request, $data);

            return response()->json(['is_valid' => false, 'error_message' => 'رقم المحرك غير مطابق', 'discount_percent' => null]);
        }

        if (isset($data['user_lat'], $data['user_lon']) && $station->latitude && $station->longitude) {
            $distanceMeters = $this->geo->distanceMeters((float) $data['user_lat'], (float) $data['user_lon'], (float) $station->latitude, (float) $station->longitude);
            if ($distanceMeters > 100) {
                $this->logSecurityEvent($data['user_id'], 'refuel_qr_validation', false, sprintf('الموقع بعيد جداً (%.0f متر)', $distanceMeters), $request, $data, (float) $station->latitude, (float) $station->longitude, $distanceMeters);

                return response()->json(['is_valid' => false, 'error_message' => sprintf('الموقع بعيد جداً (%.0f متر)', $distanceMeters)]);
            }
        }

        $subscription = $this->subscriptions->resetMonthlyLiters($subscription);
        $priorityCard = ElectronicCard::query()->where('user_id', $user->id)->where('is_used', false)->where('expires_at', '>=', now())->first();

        $this->logSecurityEvent($data['user_id'], 'refuel_qr_validation', true, null, $request, $data, $station->latitude ? (float) $station->latitude : null, $station->longitude ? (float) $station->longitude : null);

        return response()->json([
            'is_valid' => true,
            'user_info' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'vehicle_type' => $user->vehicle_type,
                'engine_number' => $user->engine_number,
            ],
            'subscription_info' => [
                'plan_type' => $subscription->plan_type,
                'status' => $subscription->status,
                'monthly_liters_used' => (float) $subscription->monthly_liters,
                'next_reward_at' => 500.0,
                'progress' => min(((float) $subscription->monthly_liters / 500) * 100, 100.0),
            ],
            'priority_info' => [
                'has_priority' => (bool) $priorityCard,
                'card_number' => $priorityCard?->card_number,
                'message' => $priorityCard ? 'عميل مميز - أولوية في الطابور' : null,
            ],
        ]);
    }

    public function processRefuel(Request $request): JsonResponse
    {
        $employee = $this->ensureWorker($request);
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'station_id' => ['required', 'integer', 'exists:gas_stations,id'],
            'liters' => ['required', 'numeric', 'min:0.01'],
            'price_per_liter' => ['required', 'numeric', 'min:0.01'],
            'qr_code' => ['required', 'string'],
        ]);

        $subscription = Subscription::query()->where('user_id', $data['user_id'])->where('status', 'active')->where('end_date', '>=', now())->first();
        $totalBefore = (float) $data['liters'] * (float) $data['price_per_liter'];
        $discount = $subscription ? $totalBefore * ((int) $subscription->discount_percent / 100) : 0;
        $final = $totalBefore - $discount;

        $refuel = Refuel::create([
            'user_id' => $data['user_id'],
            'station_id' => $data['station_id'],
            'subscription_id' => $subscription?->id,
            'employee_id' => $employee->id,
            'liters' => $data['liters'],
            'price_per_liter' => $data['price_per_liter'],
            'total_before_discount' => $totalBefore,
            'discount_amount' => $discount,
            'final_price' => $final,
            'qr_code_used' => $data['qr_code'],
            'refuel_date' => now(),
        ]);

        if ($subscription) {
            $subscription = $this->subscriptions->addMonthlyLiters($subscription, (float) $data['liters']);
            $this->subscriptions->generatePriorityCardIfEligible($subscription);
        }

        return response()->json($refuel, 201);
    }

    public function processCarWash(Request $request): JsonResponse
    {
        $employee = $this->ensureWorker($request);
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'center_id' => ['required', 'integer', 'exists:car_wash_centers,id'],
            'wash_type' => ['required', 'string', 'max:50'],
            'qr_code' => ['required', 'string'],
        ]);

        $subscription = Subscription::query()->where('user_id', $data['user_id'])->where('status', 'active')->where('remaining_car_washes', '>', 0)->first();
        abort_if(! $subscription, 400, 'No remaining car washes');

        $wash = CarWash::create([
            'user_id' => $data['user_id'],
            'center_id' => $data['center_id'],
            'subscription_id' => $subscription->id,
            'employee_id' => $employee->id,
            'wash_type' => $data['wash_type'],
            'qr_code_used' => $data['qr_code'],
            'wash_date' => now(),
        ]);

        $subscription->decrement('remaining_car_washes');

        return response()->json($wash, 201);
    }

    public function validateCarWashQr(Request $request): JsonResponse
    {
        $this->ensureWorker($request);
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'center_id' => ['nullable', 'integer', 'exists:car_wash_centers,id'],
            'user_lat' => ['nullable', 'numeric'],
            'user_lon' => ['nullable', 'numeric'],
        ]);

        $subscription = Subscription::query()->where('user_id', $data['user_id'])->where('status', 'active')->where('remaining_car_washes', '>', 0)->first();
        if (! $subscription) {
            $this->logSecurityEvent($data['user_id'], 'car_wash_qr_validation', false, 'لا يوجد رصيد غسيل', $request, $data);

            return response()->json(['is_valid' => false, 'error_message' => 'لا يوجد رصيد غسيل']);
        }

        $center = null;
        if (! empty($data['center_id'])) {
            $center = CarWashCenter::query()->where('id', $data['center_id'])->where('is_active', true)->first();
            if (! $center) {
                $this->logSecurityEvent($data['user_id'], 'car_wash_qr_validation', false, 'مركز الغسيل غير موجود', $request, $data);

                return response()->json(['is_valid' => false, 'error_message' => 'مركز الغسيل غير موجود']);
            }

            if (isset($data['user_lat'], $data['user_lon']) && $center->latitude && $center->longitude) {
                $distanceMeters = $this->geo->distanceMeters((float) $data['user_lat'], (float) $data['user_lon'], (float) $center->latitude, (float) $center->longitude);
                if ($distanceMeters > 100) {
                    $this->logSecurityEvent($data['user_id'], 'car_wash_qr_validation', false, sprintf('الموقع بعيد جداً (%.0f متر)', $distanceMeters), $request, $data, (float) $center->latitude, (float) $center->longitude, $distanceMeters);

                    return response()->json(['is_valid' => false, 'error_message' => sprintf('الموقع بعيد جداً (%.0f متر)', $distanceMeters)]);
                }
            }
        }

        $this->logSecurityEvent($data['user_id'], 'car_wash_qr_validation', true, null, $request, $data, $center?->latitude ? (float) $center->latitude : null, $center?->longitude ? (float) $center->longitude : null);

        return response()->json(['is_valid' => true, 'discount_percent' => (int) $subscription->discount_percent]);
    }

    public function validateMaintenanceQr(Request $request): JsonResponse
    {
        $this->ensureWorker($request);
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'center_id' => ['nullable', 'integer', 'exists:maintenance_centers,id'],
            'user_lat' => ['nullable', 'numeric'],
            'user_lon' => ['nullable', 'numeric'],
        ]);

        $subscription = Subscription::query()
            ->where('user_id', $data['user_id'])
            ->where('status', 'active')
            ->where('remaining_maintenance', '>', 0)
            ->first();

        if (! $subscription) {
            $this->logSecurityEvent($data['user_id'], 'maintenance_qr_validation', false, 'لا يوجد رصيد صيانة', $request, $data);

            return response()->json(['is_valid' => false, 'error_message' => 'لا يوجد رصيد صيانة']);
        }

        $center = null;
        if (! empty($data['center_id'])) {
            $center = MaintenanceCenter::query()->where('id', $data['center_id'])->where('is_active', true)->first();
            if (! $center) {
                $this->logSecurityEvent($data['user_id'], 'maintenance_qr_validation', false, 'مركز الصيانة غير موجود', $request, $data);

                return response()->json(['is_valid' => false, 'error_message' => 'مركز الصيانة غير موجود']);
            }

            if (isset($data['user_lat'], $data['user_lon']) && $center->latitude && $center->longitude) {
                $distanceMeters = $this->geo->distanceMeters((float) $data['user_lat'], (float) $data['user_lon'], (float) $center->latitude, (float) $center->longitude);
                if ($distanceMeters > 100) {
                    $this->logSecurityEvent($data['user_id'], 'maintenance_qr_validation', false, sprintf('الموقع بعيد جداً (%.0f متر)', $distanceMeters), $request, $data, (float) $center->latitude, (float) $center->longitude, $distanceMeters);

                    return response()->json(['is_valid' => false, 'error_message' => sprintf('الموقع بعيد جداً (%.0f متر)', $distanceMeters)]);
                }
            }
        }

        $this->logSecurityEvent($data['user_id'], 'maintenance_qr_validation', true, null, $request, $data, $center?->latitude ? (float) $center->latitude : null, $center?->longitude ? (float) $center->longitude : null);

        return response()->json([
            'is_valid' => true,
            'remaining_maintenance' => $subscription->remaining_maintenance,
            'discount_percent' => (int) $subscription->discount_percent,
        ]);
    }

    public function processMaintenance(Request $request): JsonResponse
    {
        $employee = $this->ensureWorker($request);
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'center_id' => ['required', 'integer', 'exists:maintenance_centers,id'],
            'service_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'cost' => ['required', 'numeric', 'min:0'],
            'next_service_date' => ['nullable', 'date'],
            'qr_code' => ['required', 'string'],
        ]);

        $subscription = Subscription::query()->where('user_id', $data['user_id'])->where('status', 'active')->where('remaining_maintenance', '>', 0)->first();
        abort_if(! $subscription, 400, 'No maintenance credit');

        $maintenance = MaintenanceService::create([
            'user_id' => $data['user_id'],
            'center_id' => $data['center_id'],
            'subscription_id' => $subscription->id,
            'employee_id' => $employee->id,
            'service_type' => $data['service_type'],
            'description' => $data['description'] ?? null,
            'cost' => $data['cost'],
            'qr_code_used' => $data['qr_code'],
            'service_date' => now(),
            'next_service_date' => $data['next_service_date'] ?? null,
        ]);

        $subscription->decrement('remaining_maintenance');

        return response()->json($maintenance, 201);
    }

    public function employeeRefuels(Request $request, int $employeeId): JsonResponse
    {
        $this->ensureWorker($request);
        return response()->json(Refuel::query()->where('employee_id', $employeeId)->latest('refuel_date')->get());
    }

    public function employeeCarWashes(Request $request, int $employeeId): JsonResponse
    {
        $this->ensureWorker($request);
        return response()->json(CarWash::query()->where('employee_id', $employeeId)->latest('wash_date')->get());
    }

    public function employeeMaintenance(Request $request, int $employeeId): JsonResponse
    {
        $this->ensureWorker($request);
        return response()->json(MaintenanceService::query()->where('employee_id', $employeeId)->latest('service_date')->get());
    }

    private function logSecurityEvent(
        int $userId,
        string $type,
        bool $successful,
        ?string $errorMessage,
        Request $request,
        array $payload,
        ?float $serviceLat = null,
        ?float $serviceLon = null,
        ?float $distanceMeters = null,
    ): void {
        SecurityLog::create([
            'user_id' => $userId,
            'log_type' => $type,
            'qr_code' => $payload['qr_code'] ?? null,
            'ip_address' => $request->ip(),
            'user_lat' => isset($payload['user_lat']) ? (float) $payload['user_lat'] : null,
            'user_lon' => isset($payload['user_lon']) ? (float) $payload['user_lon'] : null,
            'service_lat' => $serviceLat,
            'service_lon' => $serviceLon,
            'distance_meters' => $distanceMeters,
            'is_successful' => $successful,
            'error_message' => $errorMessage,
            'created_at' => now(),
        ]);
    }
}
