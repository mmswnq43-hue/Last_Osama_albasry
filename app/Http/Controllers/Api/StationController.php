<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GasStation;
use App\Services\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function __construct(
        private readonly GeoService $geo,
    ) {
    }

    public function nearby(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lon' => ['required', 'numeric'],
            'radius_km' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
        ]);

        $radius = (float) ($data['radius_km'] ?? 10.0);
        $nearby = [];

        foreach (GasStation::query()->where('is_active', true)->get() as $station) {
            if (! $station->latitude || ! $station->longitude) {
                continue;
            }

            $distance = $this->geo->distanceKm((float) $data['lat'], (float) $data['lon'], (float) $station->latitude, (float) $station->longitude);
            if ($distance <= $radius) {
                $nearby[] = [
                    'id' => $station->id,
                    'station_name' => $station->station_name,
                    'location' => $station->location,
                    'distance_km' => round($distance, 2),
                    'latitude' => (float) $station->latitude,
                    'longitude' => (float) $station->longitude,
                ];
            }
        }

        usort($nearby, fn ($left, $right) => $left['distance_km'] <=> $right['distance_km']);

        return response()->json($nearby);
    }

    public function index(Request $request): JsonResponse
    {
        $query = GasStation::query()->where('is_active', true);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($inner) use ($search): void {
                $inner->where('station_name', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%');
            });
        }

        return response()->json($query->get());
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(GasStation::query()->findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()?->user_role === 'admin', 403, 'Requires Admin role');

        $data = $request->validate([
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'station_name' => ['required', 'string', 'max:100'],
            'commercial_register' => ['required', 'string', 'max:50', 'unique:gas_stations,commercial_register'],
            'location' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'station_code' => ['required', 'string', 'max:20', 'unique:gas_stations,station_code'],
        ]);

        return response()->json(GasStation::create($data), 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        abort_unless(in_array($request->user()?->user_role, ['station_owner', 'admin'], true), 403, 'Requires Owner role');

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

    public function destroy(Request $request, int $id): JsonResponse
    {
        abort_unless($request->user()?->user_role === 'admin', 403, 'Requires Admin role');
        GasStation::query()->findOrFail($id)->delete();

        return response()->json([], 204);
    }
}
