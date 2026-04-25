<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ElectronicCard;
use App\Models\GasStation;
use App\Services\GasYemenNotificationService;
use App\Services\GasYemenSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElectronicCardController extends Controller
{
    public function __construct(
        private readonly GasYemenNotificationService $notifications,
        private readonly GasYemenSubscriptionService $subscriptions,
    ) {
    }

    public function myCards(Request $request): JsonResponse
    {
        return response()->json(
            ElectronicCard::query()->where('user_id', $request->user()->id)->latest('generated_at')->get()
        );
    }

    public function show(Request $request, string $cardNumber): JsonResponse
    {
        $card = ElectronicCard::query()
            ->where('card_number', $cardNumber)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json($card);
    }

    public function usePriority(Request $request, string $cardNumber): JsonResponse
    {
        $data = $request->validate([
            'station_id' => ['required', 'integer', 'exists:gas_stations,id'],
        ]);

        $card = ElectronicCard::query()
            ->where('card_number', $cardNumber)
            ->where('user_id', $request->user()->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $station = GasStation::query()->findOrFail($data['station_id']);

        $card->forceFill([
            'is_used' => true,
            'used_at' => now(),
            'priority_station_id' => $station->id,
        ])->save();

        $this->notifications->createSystemNotification(
            $request->user()->id,
            'استخدام كرت الأولوية',
            'تم استخدام كرت الأولوية الخاص بك في محطة '.$station->station_name,
            'priority_card_used',
        );

        return response()->json([
            'message' => 'Priority card used successfully',
            'station_name' => $station->station_name,
            'card_number' => $cardNumber,
            'used_at' => $card->used_at,
            'priority_access' => true,
        ]);
    }

    public function priorityStatus(Request $request): JsonResponse
    {
        $activeCards = ElectronicCard::query()
            ->where('user_id', $request->user()->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->get();

        $subscription = $this->subscriptions->activeSubscription($request->user());
        $monthlyLiters = $subscription ? (float) $subscription->monthly_liters : 0.0;

        return response()->json([
            'active_priority_cards' => $activeCards->count(),
            'monthly_fuel_used' => $monthlyLiters,
            'fuel_until_next_card' => max(0, 500 - $monthlyLiters),
            'progress_to_next_card' => $monthlyLiters > 0 ? ($monthlyLiters / 500) * 100 : 0,
            'cards' => $activeCards->map(fn (ElectronicCard $card) => [
                'card_number' => $card->card_number,
                'expires_at' => $card->expires_at,
                'monthly_fuel_at_generation' => (float) $card->monthly_liters_at_generation,
            ])->values(),
        ]);
    }

    public function validateCard(Request $request, string $cardNumber): JsonResponse
    {
        $card = ElectronicCard::query()->where('card_number', $cardNumber)->first();
        if (! $card) {
            return response()->json(['is_valid' => false, 'message' => 'Card not found']);
        }

        if ($card->expires_at && $card->expires_at->isPast()) {
            return response()->json(['is_valid' => false, 'message' => 'Card has expired']);
        }

        if ($card->is_used) {
            return response()->json(['is_valid' => false, 'message' => 'Card has already been used']);
        }

        return response()->json([
            'is_valid' => true,
            'message' => 'Card is valid for priority fueling',
            'user_info' => [
                'name' => $card->user?->full_name,
                'card_number' => $card->card_number,
                'expires_at' => $card->expires_at,
            ],
        ]);
    }

    public function allCards(Request $request): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Admin only');

        return response()->json(ElectronicCard::query()->latest('generated_at')->get());
    }

    public function userCards(Request $request, int $userId): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Admin only');

        return response()->json(
            ElectronicCard::query()->where('user_id', $userId)->latest('generated_at')->get()
        );
    }
}
