<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Models\GasStation;
use App\Models\MaintenanceService;
use App\Models\Notification;
use App\Models\Refuel;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\GasYemenSubscriptionService;
use App\Services\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly GasYemenSubscriptionService $subscriptions,
        private readonly GeoService $geo,
    ) {
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    public function updateMe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['sometimes', 'string', 'max:100'],
            'vehicle_type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'engine_number' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $request->user()->fill($data)->save();

        return response()->json($request->user()->refresh());
    }

    public function show(Request $request, int $userId): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Admin only');

        return response()->json(User::query()->findOrFail($userId));
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        $favoriteStation = GasStation::query()
            ->select('gas_stations.station_name')
            ->join('refuels', 'refuels.station_id', '=', 'gas_stations.id')
            ->where('refuels.user_id', $user->id)
            ->groupBy('gas_stations.id', 'gas_stations.station_name')
            ->orderByRaw('COUNT(refuels.id) DESC')
            ->value('station_name');

        $activeSubscription = Subscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest('end_date')
            ->first();

        return response()->json([
            'total_refuels' => Refuel::query()->where('user_id', $user->id)->count(),
            'total_cylinders' => (float) Refuel::query()->where('user_id', $user->id)->sum('liters'),
            'total_spent' => (float) Refuel::query()->where('user_id', $user->id)->sum('final_price'),
            'total_saved' => (float) Refuel::query()->where('user_id', $user->id)->sum('discount_amount'),
            'favorite_station' => $favoriteStation,
            'last_refuel_date' => Refuel::query()->where('user_id', $user->id)->max('refuel_date'),
            'remaining_car_washes' => $activeSubscription?->remaining_car_washes ?? 0,
            'remaining_maintenance' => $activeSubscription?->remaining_maintenance ?? 0,
        ]);
    }

    public function stations(Request $request): JsonResponse
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

    public function nearbyStations(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric'],
            'lon' => ['required', 'numeric'],
            'radius_km' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
        ]);

        $radius = (float) ($data['radius_km'] ?? 10);
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
                    'distance_km' => round($distance, 2),
                ];
            }
        }

        usort($nearby, fn ($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        return response()->json($nearby);
    }

    public function plans(): JsonResponse
    {
        return response()->json(array_values($this->subscriptions->plans()));
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan_type' => ['required', 'string'],
        ]);

        $subscription = $this->subscriptions->createSubscription($request->user(), $data['plan_type'], 'pending');

        return response()->json($subscription, 201);
    }

    public function refuels(Request $request): JsonResponse
    {
        return response()->json(Refuel::query()->where('user_id', $request->user()->id)->latest('refuel_date')->get());
    }

    public function carWashes(Request $request): JsonResponse
    {
        return response()->json(CarWash::query()->where('user_id', $request->user()->id)->latest('wash_date')->get());
    }

    public function maintenance(Request $request): JsonResponse
    {
        return response()->json(MaintenanceService::query()->where('user_id', $request->user()->id)->latest('service_date')->get());
    }

    public function qrCodes(Request $request): JsonResponse
    {
        $serviceType = $request->query('service_type');
        $subscription = $this->subscriptions->activeSubscription($request->user());
        abort_if(! $subscription, 403, 'No active subscription found');

        return response()->json($this->subscriptions->qrPayload($request->user(), $subscription, $serviceType));
    }

    public function generateQrCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_type' => ['required', 'string', 'in:refuel,car_wash,maintenance'],
        ]);

        $subscription = $this->subscriptions->activeSubscription($request->user());
        abort_if(! $subscription, 403, 'No active subscription found');

        $payload = $this->subscriptions->qrPayload($request->user(), $subscription, $data['service_type']);
        $qrCode = $payload['qr_codes'][$data['service_type'].'_qr'] ?? null;

        abort_if(! $qrCode, 403, 'Requested service is not available');

        return response()->json([
            'service_type' => $data['service_type'],
            'qr_code' => $qrCode,
            'expires_at' => $payload['expires_at'],
            'user_id' => $request->user()->id,
            'generated_at' => now(),
        ]);
    }

    public function createTicket(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', 'string', 'max:20'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'open',
        ]);

        return response()->json($ticket, 201);
    }

    public function notifications(Request $request): JsonResponse
    {
        return response()->json(
            Notification::query()->where('user_id', $request->user()->id)->latest('created_at')->get()
        );
    }
}
