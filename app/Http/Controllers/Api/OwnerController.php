<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\CarWashCenter;
use App\Models\Employee;
use App\Models\GasStation;
use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use App\Models\Refuel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OwnerController extends Controller
{
    private function ensureOwner(Request $request): void
    {
        abort_unless(in_array($request->user()->user_role, ['station_owner', 'car_wash_owner', 'maintenance_owner', 'admin'], true), 403, 'Requires Owner role');
    }

    public function myStations(Request $request): JsonResponse
    {
        abort_unless($request->user()->user_role === 'station_owner' || $request->user()->user_role === 'admin', 403, 'Station owner only');
        return response()->json(GasStation::query()->where('owner_id', $request->user()->id)->get());
    }

    public function myCarWashes(Request $request): JsonResponse
    {
        abort_unless($request->user()->user_role === 'car_wash_owner' || $request->user()->user_role === 'admin', 403, 'Car wash owner only');
        return response()->json(CarWashCenter::query()->where('owner_id', $request->user()->id)->get());
    }

    public function myMaintenanceCenters(Request $request): JsonResponse
    {
        abort_unless($request->user()->user_role === 'maintenance_owner' || $request->user()->user_role === 'admin', 403, 'Maintenance owner only');
        return response()->json(MaintenanceCenter::query()->where('owner_id', $request->user()->id)->get());
    }

    public function myBusiness(Request $request): JsonResponse
    {
        $this->ensureOwner($request);

        $user = $request->user();
        $overview = [
            'owner_type' => $user->user_role,
            'businesses' => [],
            'stats' => [],
        ];

        if ($user->user_role === 'station_owner' || $user->user_role === 'admin') {
            $stations = GasStation::query()->where('owner_id', $user->id)->with('employees')->get();
            $overview['businesses']['stations'] = $stations->map(fn (GasStation $station) => [
                'id' => $station->id,
                'name' => $station->station_name,
                'location' => $station->location,
                'is_active' => $station->is_active,
                'employees_count' => $station->employees->where('is_active', true)->count(),
            ])->values();
            $overview['stats'] = [
                'total_stations' => $stations->count(),
                'active_stations' => $stations->where('is_active', true)->count(),
                'total_refuels' => Refuel::query()->whereIn('station_id', $stations->pluck('id'))->count(),
                'monthly_revenue' => (float) Refuel::query()->whereIn('station_id', $stations->pluck('id'))->where('refuel_date', '>=', now()->subDays(30))->sum('final_price'),
            ];
        }

        if ($user->user_role === 'car_wash_owner') {
            $centers = CarWashCenter::query()->where('owner_id', $user->id)->with('employees')->get();
            $overview['businesses']['car_washes'] = $centers->map(fn (CarWashCenter $center) => [
                'id' => $center->id,
                'name' => $center->center_name,
                'location' => $center->location,
                'is_active' => $center->is_active,
                'employees_count' => $center->employees->where('is_active', true)->count(),
            ])->values();
            $overview['stats'] = [
                'total_centers' => $centers->count(),
                'active_centers' => $centers->where('is_active', true)->count(),
                'total_washes' => CarWash::query()->whereIn('center_id', $centers->pluck('id'))->count(),
            ];
        }

        if ($user->user_role === 'maintenance_owner') {
            $centers = MaintenanceCenter::query()->where('owner_id', $user->id)->with('employees')->get();
            $overview['businesses']['maintenance_centers'] = $centers->map(fn (MaintenanceCenter $center) => [
                'id' => $center->id,
                'name' => $center->center_name,
                'location' => $center->location,
                'is_active' => $center->is_active,
                'specialization' => $center->specialization,
                'employees_count' => $center->employees->where('is_active', true)->count(),
            ])->values();
            $overview['stats'] = [
                'total_centers' => $centers->count(),
                'active_centers' => $centers->where('is_active', true)->count(),
                'total_services' => MaintenanceService::query()->whereIn('center_id', $centers->pluck('id'))->count(),
            ];
        }

        return response()->json($overview);
    }

    public function myEmployees(Request $request): JsonResponse
    {
        $this->ensureOwner($request);

        $stationIds = GasStation::query()->where('owner_id', $request->user()->id)->pluck('id');
        $washIds = CarWashCenter::query()->where('owner_id', $request->user()->id)->pluck('id');
        $maintenanceIds = MaintenanceCenter::query()->where('owner_id', $request->user()->id)->pluck('id');

        $employees = Employee::query()
            ->whereIn('station_id', $stationIds)
            ->orWhereIn('car_wash_center_id', $washIds)
            ->orWhereIn('maintenance_center_id', $maintenanceIds)
            ->get();

        return response()->json($employees);
    }

    public function addEmployee(Request $request): JsonResponse
    {
        $this->ensureOwner($request);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'station_id' => ['nullable', 'integer', 'exists:gas_stations,id'],
            'car_wash_center_id' => ['nullable', 'integer', 'exists:car_wash_centers,id'],
            'maintenance_center_id' => ['nullable', 'integer', 'exists:maintenance_centers,id'],
            'position' => ['required', 'string', 'max:50'],
            'salary' => ['nullable', 'numeric'],
            'hire_date' => ['required', 'date'],
        ]);

        $workplaces = collect(['station_id', 'car_wash_center_id', 'maintenance_center_id'])
            ->filter(fn ($key) => ! empty($data[$key]));
        abort_if($workplaces->count() !== 1, 422, 'Employee must belong to exactly one workplace');

        if (! empty($data['station_id'])) {
            abort_unless(GasStation::query()->where('id', $data['station_id'])->where('owner_id', $request->user()->id)->exists(), 403, 'Unauthorized for this station');
        }
        if (! empty($data['car_wash_center_id'])) {
            abort_unless(CarWashCenter::query()->where('id', $data['car_wash_center_id'])->where('owner_id', $request->user()->id)->exists(), 403, 'Unauthorized for this car wash center');
        }
        if (! empty($data['maintenance_center_id'])) {
            abort_unless(MaintenanceCenter::query()->where('id', $data['maintenance_center_id'])->where('owner_id', $request->user()->id)->exists(), 403, 'Unauthorized for this maintenance center');
        }

        $user = User::query()->findOrFail($data['user_id']);
        if (! empty($data['station_id'])) {
            $user->user_role = 'station_worker';
        } elseif (! empty($data['car_wash_center_id'])) {
            $user->user_role = 'car_wash_worker';
        } else {
            $user->user_role = 'maintenance_worker';
        }
        $user->save();

        $employee = Employee::create([
            ...$data,
            'employee_code' => 'EMP-'.Str::upper(Str::random(8)),
            'is_active' => true,
        ]);

        return response()->json($employee, 201);
    }

    public function revenue(Request $request): JsonResponse
    {
        $this->ensureOwner($request);
        $period = $request->input('period', 'monthly');
        $start = $period === 'monthly' ? now()->subDays(30) : now()->subDay();
        $stationIds = GasStation::query()->where('owner_id', $request->user()->id)->pluck('id');

        return response()->json([
            'period' => $period,
            'total_revenue' => (float) Refuel::query()->whereIn('station_id', $stationIds)->where('refuel_date', '>=', $start)->sum('final_price'),
            'total_liters' => (float) Refuel::query()->whereIn('station_id', $stationIds)->where('refuel_date', '>=', $start)->sum('liters'),
            'station_count' => $stationIds->count(),
        ]);
    }

    public function updateStation(Request $request, int $id): JsonResponse
    {
        $this->ensureOwner($request);
        $data = $request->validate([
            'station_name' => ['required', 'string', 'max:100'],
            'commercial_register' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'station_code' => ['required', 'string', 'max:20'],
        ]);

        $station = GasStation::query()->findOrFail($id);
        abort_if($station->owner_id !== $request->user()->id && $request->user()->user_role !== 'admin', 403, 'Not authorized to update this station');
        $station->fill($data)->save();

        return response()->json($station->refresh());
    }

    public function stationTodayRefuels(Request $request, int $stationId): JsonResponse
    {
        $this->ensureOwner($request);
        abort_unless(GasStation::query()->where('id', $stationId)->where('owner_id', $request->user()->id)->exists() || $request->user()->user_role === 'admin', 403, 'Unauthorized');
        $start = now()->startOfDay();

        return response()->json([
            'date' => now()->toDateString(),
            'count' => Refuel::query()->where('station_id', $stationId)->where('refuel_date', '>=', $start)->count(),
            'total_liters' => (float) Refuel::query()->where('station_id', $stationId)->where('refuel_date', '>=', $start)->sum('liters'),
            'total_revenue' => (float) Refuel::query()->where('station_id', $stationId)->where('refuel_date', '>=', $start)->sum('final_price'),
        ]);
    }

    public function washCenterToday(Request $request, int $centerId): JsonResponse
    {
        $this->ensureOwner($request);
        abort_unless(CarWashCenter::query()->where('id', $centerId)->where('owner_id', $request->user()->id)->exists() || $request->user()->user_role === 'admin', 403, 'Unauthorized');

        return response()->json([
            'date' => now()->toDateString(),
            'total_washes' => CarWash::query()->where('center_id', $centerId)->where('wash_date', '>=', now()->startOfDay())->count(),
        ]);
    }

    public function maintenanceToday(Request $request, int $centerId): JsonResponse
    {
        $this->ensureOwner($request);
        abort_unless(MaintenanceCenter::query()->where('id', $centerId)->where('owner_id', $request->user()->id)->exists() || $request->user()->user_role === 'admin', 403, 'Unauthorized');

        return response()->json([
            'date' => now()->toDateString(),
            'total_services' => MaintenanceService::query()->where('center_id', $centerId)->where('service_date', '>=', now()->startOfDay())->count(),
            'total_cost' => (float) MaintenanceService::query()->where('center_id', $centerId)->where('service_date', '>=', now()->startOfDay())->sum('cost'),
        ]);
    }
}
