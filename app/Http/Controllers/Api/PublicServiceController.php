<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarWashCenter;
use App\Models\GasStation;
use App\Models\MaintenanceCenter;
use App\Services\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicServiceController extends Controller
{
    public function __construct(
        private readonly GeoService $geo,
    ) {
    }

    public function carWashes(Request $request): JsonResponse
    {
        $query = CarWashCenter::query()->where('is_active', true);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($inner) use ($search): void {
                $inner->where('center_name', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%');
            });
        }

        if ($city = $request->string('city')->toString()) {
            $query->where('location', 'like', '%'.$city.'%');
        }

        return response()->json($query->get()->map(fn (CarWashCenter $center) => [
            'id' => $center->id,
            'center_name' => $center->center_name,
            'location' => $center->location,
            'city' => str_contains($center->location, ',') ? explode(',', $center->location)[0] : $center->location,
            'latitude' => $center->latitude ? (float) $center->latitude : null,
            'longitude' => $center->longitude ? (float) $center->longitude : null,
            'center_code' => $center->center_code,
            'is_active' => $center->is_active,
        ])->values());
    }

    public function carWashDetails(int $id): JsonResponse
    {
        $center = CarWashCenter::query()->where('id', $id)->where('is_active', true)->firstOrFail();

        return response()->json([
            'id' => $center->id,
            'center_name' => $center->center_name,
            'location' => $center->location,
            'latitude' => $center->latitude ? (float) $center->latitude : null,
            'longitude' => $center->longitude ? (float) $center->longitude : null,
            'center_code' => $center->center_code,
            'is_active' => $center->is_active,
            'created_at' => $center->created_at,
            'services_offered' => ['basic', 'premium', 'deluxe'],
            'working_hours' => [
                'saturday' => '08:00 - 20:00',
                'sunday' => '08:00 - 20:00',
                'monday' => '08:00 - 20:00',
                'tuesday' => '08:00 - 20:00',
                'wednesday' => '08:00 - 20:00',
                'thursday' => '08:00 - 20:00',
                'friday' => '08:00 - 20:00',
            ],
            'contact_info' => [
                'phone' => '+967'.$center->center_code,
                'email' => 'wash'.$center->id.'@gasyemen.com',
            ],
        ]);
    }

    public function nearbyCarWashes(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lon' => ['required', 'numeric'],
            'radius_km' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
        ]);

        $radius = (float) ($data['radius_km'] ?? 10);
        $results = [];

        foreach (CarWashCenter::query()->where('is_active', true)->get() as $center) {
            if (! $center->latitude || ! $center->longitude) {
                continue;
            }

            $distance = $this->geo->distanceKm((float) $data['lat'], (float) $data['lon'], (float) $center->latitude, (float) $center->longitude);
            if ($distance <= $radius) {
                $results[] = [
                    'id' => $center->id,
                    'center_name' => $center->center_name,
                    'location' => $center->location,
                    'distance_km' => round($distance, 2),
                    'latitude' => (float) $center->latitude,
                    'longitude' => (float) $center->longitude,
                    'center_code' => $center->center_code,
                ];
            }
        }

        usort($results, fn ($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        return response()->json($results);
    }

    public function maintenanceCenters(Request $request): JsonResponse
    {
        $query = MaintenanceCenter::query()->where('is_active', true);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($inner) use ($search): void {
                $inner->where('center_name', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%')
                    ->orWhere('specialization', 'like', '%'.$search.'%');
            });
        }

        if ($specialization = $request->string('specialization')->toString()) {
            $query->where('specialization', 'like', '%'.$specialization.'%');
        }

        if ($city = $request->string('city')->toString()) {
            $query->where('location', 'like', '%'.$city.'%');
        }

        return response()->json($query->get()->map(fn (MaintenanceCenter $center) => [
            'id' => $center->id,
            'center_name' => $center->center_name,
            'location' => $center->location,
            'city' => str_contains($center->location, ',') ? explode(',', $center->location)[0] : $center->location,
            'specialization' => $center->specialization,
            'latitude' => $center->latitude ? (float) $center->latitude : null,
            'longitude' => $center->longitude ? (float) $center->longitude : null,
            'center_code' => $center->center_code,
            'is_active' => $center->is_active,
        ])->values());
    }

    public function maintenanceCenterDetails(int $id): JsonResponse
    {
        $center = MaintenanceCenter::query()->where('id', $id)->where('is_active', true)->firstOrFail();

        return response()->json([
            'id' => $center->id,
            'center_name' => $center->center_name,
            'location' => $center->location,
            'latitude' => $center->latitude ? (float) $center->latitude : null,
            'longitude' => $center->longitude ? (float) $center->longitude : null,
            'specialization' => $center->specialization,
            'center_code' => $center->center_code,
            'is_active' => $center->is_active,
            'created_at' => $center->created_at,
            'services_offered' => ['تغيير زيت', 'فحص شامل', 'صيانة مكابح', 'تغيير فلاتر'],
            'working_hours' => [
                'saturday' => '08:00 - 18:00',
                'sunday' => '08:00 - 18:00',
                'monday' => '08:00 - 18:00',
                'tuesday' => '08:00 - 18:00',
                'wednesday' => '08:00 - 18:00',
                'thursday' => '08:00 - 18:00',
                'friday' => '08:00 - 18:00',
            ],
            'contact_info' => [
                'phone' => '+967'.$center->center_code,
                'email' => 'maintenance'.$center->id.'@gasyemen.com',
            ],
        ]);
    }

    public function nearbyMaintenanceCenters(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lon' => ['required', 'numeric'],
            'radius_km' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
            'specialization' => ['nullable', 'string'],
        ]);

        $query = MaintenanceCenter::query()->where('is_active', true);
        if (! empty($data['specialization'])) {
            $query->where('specialization', 'like', '%'.$data['specialization'].'%');
        }

        $radius = (float) ($data['radius_km'] ?? 10);
        $results = [];

        foreach ($query->get() as $center) {
            if (! $center->latitude || ! $center->longitude) {
                continue;
            }

            $distance = $this->geo->distanceKm((float) $data['lat'], (float) $data['lon'], (float) $center->latitude, (float) $center->longitude);
            if ($distance <= $radius) {
                $results[] = [
                    'id' => $center->id,
                    'center_name' => $center->center_name,
                    'location' => $center->location,
                    'specialization' => $center->specialization,
                    'distance_km' => round($distance, 2),
                    'latitude' => (float) $center->latitude,
                    'longitude' => (float) $center->longitude,
                    'center_code' => $center->center_code,
                ];
            }
        }

        usort($results, fn ($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        return response()->json($results);
    }

    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'query' => ['required', 'string'],
            'service_type' => ['nullable', 'string'],
            'lat' => ['nullable', 'numeric'],
            'lon' => ['nullable', 'numeric'],
            'radius_km' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
        ]);

        $radius = (float) ($data['radius_km'] ?? 10);
        $query = $data['query'];
        $results = [
            'stations' => [],
            'car_washes' => [],
            'maintenance_centers' => [],
        ];

        if (empty($data['service_type']) || $data['service_type'] === 'station') {
            foreach (GasStation::query()->where('is_active', true)->where(function ($inner) use ($query): void {
                $inner->where('station_name', 'like', '%'.$query.'%')
                    ->orWhere('location', 'like', '%'.$query.'%');
            })->get() as $station) {
                if (! isset($data['lat'], $data['lon']) || ! $station->latitude || ! $station->longitude) {
                    continue;
                }

                $distance = $this->geo->distanceKm((float) $data['lat'], (float) $data['lon'], (float) $station->latitude, (float) $station->longitude);
                if ($distance <= $radius) {
                    $results['stations'][] = [
                        'id' => $station->id,
                        'name' => $station->station_name,
                        'location' => $station->location,
                        'distance_km' => round($distance, 2),
                        'type' => 'gas_station',
                    ];
                }
            }
        }

        if (empty($data['service_type']) || $data['service_type'] === 'car_wash') {
            foreach (CarWashCenter::query()->where('is_active', true)->where(function ($inner) use ($query): void {
                $inner->where('center_name', 'like', '%'.$query.'%')
                    ->orWhere('location', 'like', '%'.$query.'%');
            })->get() as $center) {
                if (! isset($data['lat'], $data['lon']) || ! $center->latitude || ! $center->longitude) {
                    continue;
                }

                $distance = $this->geo->distanceKm((float) $data['lat'], (float) $data['lon'], (float) $center->latitude, (float) $center->longitude);
                if ($distance <= $radius) {
                    $results['car_washes'][] = [
                        'id' => $center->id,
                        'name' => $center->center_name,
                        'location' => $center->location,
                        'distance_km' => round($distance, 2),
                        'type' => 'car_wash',
                    ];
                }
            }
        }

        if (empty($data['service_type']) || $data['service_type'] === 'maintenance') {
            foreach (MaintenanceCenter::query()->where('is_active', true)->where(function ($inner) use ($query): void {
                $inner->where('center_name', 'like', '%'.$query.'%')
                    ->orWhere('location', 'like', '%'.$query.'%')
                    ->orWhere('specialization', 'like', '%'.$query.'%');
            })->get() as $center) {
                if (! isset($data['lat'], $data['lon']) || ! $center->latitude || ! $center->longitude) {
                    continue;
                }

                $distance = $this->geo->distanceKm((float) $data['lat'], (float) $data['lon'], (float) $center->latitude, (float) $center->longitude);
                if ($distance <= $radius) {
                    $results['maintenance_centers'][] = [
                        'id' => $center->id,
                        'name' => $center->center_name,
                        'location' => $center->location,
                        'specialization' => $center->specialization,
                        'distance_km' => round($distance, 2),
                        'type' => 'maintenance_center',
                    ];
                }
            }
        }

        return response()->json($results);
    }
}
