<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ElectronicCard;
use App\Models\Refuel;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\GasYemenNotificationService;
use App\Services\GasYemenSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefuelController extends Controller
{
    public function __construct(
        private readonly GasYemenSubscriptionService $subscriptions,
        private readonly GasYemenNotificationService $notifications,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'station_id' => ['required', 'integer', 'exists:gas_stations,id'],
            'liters' => ['required', 'numeric', 'min:0.01'],
            'price_per_liter' => ['required', 'numeric', 'min:0.01'],
            'qr_code' => ['required', 'string'],
            'payment_method' => ['nullable', 'string'],
        ]);

        $customer = User::query()
            ->where('qr_code', $data['qr_code'])
            ->orWhere('id', $data['user_id'] ?? 0)
            ->first();

        abort_if(! $customer, 404, 'Customer not found with this QR code');

        $subscription = $this->subscriptions->activeSubscription($customer);
        $totalBefore = (float) $data['liters'] * (float) $data['price_per_liter'];
        $discount = $subscription ? $totalBefore * ((int) $subscription->discount_percent / 100) : 0;
        $finalPrice = $totalBefore - $discount;

        $refuel = Refuel::create([
            'user_id' => $customer->id,
            'station_id' => $data['station_id'],
            'subscription_id' => $subscription?->id,
            'employee_id' => null,
            'liters' => $data['liters'],
            'price_per_liter' => $data['price_per_liter'],
            'total_before_discount' => $totalBefore,
            'discount_amount' => $discount,
            'final_price' => $finalPrice,
            'qr_code_used' => $data['qr_code'],
            'refuel_date' => now(),
        ]);

        $generatedCard = null;
        if ($subscription) {
            $subscription = $this->subscriptions->addMonthlyLiters($subscription, (float) $data['liters']);
            $generatedCard = $this->subscriptions->generatePriorityCardIfEligible($subscription);

            $message = 'تم تعبئة '.(float) $data['liters'].' لتر بنجاح. إجمالي هذا الشهر: '.(float) $subscription->monthly_liters.' لتر.';
            if ($generatedCard) {
                $message .= ' تم توليد كرت أولوية جديد: '.$generatedCard->card_number;
            }

            $this->notifications->createSystemNotification(
                $customer->id,
                'تحديث التعبئة',
                $message,
                'fuel_consumption_update',
            );
        }

        return response()->json($refuel, 201);
    }

    public function fuelAnalytics(Request $request): JsonResponse
    {
        $subscription = $this->subscriptions->activeSubscription($request->user());
        abort_if(! $subscription, 404, 'No active subscription found');

        $currentMonthStart = now()->startOfMonth();
        $monthlyRefuels = Refuel::query()
            ->where('user_id', $request->user()->id)
            ->where('refuel_date', '>=', $currentMonthStart)
            ->get();

        $allRefuels = Refuel::query()->where('user_id', $request->user()->id)->get();
        $priorityCards = ElectronicCard::query()
            ->where('user_id', $request->user()->id)
            ->where('expires_at', '>', now())
            ->get();

        $monthlyLiters = (float) $subscription->monthly_liters;

        return response()->json([
            'subscription_info' => [
                'plan_type' => $subscription->plan_type,
                'end_date' => $subscription->end_date,
                'monthly_fuel_used' => $monthlyLiters,
                'last_monthly_reset' => $subscription->last_reset_date,
            ],
            'current_month_status' => [
                'total_monthly_liters' => (float) $monthlyRefuels->sum('liters'),
                'total_monthly_spent' => (float) $monthlyRefuels->sum('final_price'),
            ],
            'all_time_stats' => [
                'total_all_time_liters' => (float) $allRefuels->sum('liters'),
                'total_all_time_spent' => (float) $allRefuels->sum('final_price'),
            ],
            'priority_cards' => [
                'total_active_cards' => $priorityCards->count(),
                'unused_cards' => $priorityCards->where('is_used', false)->count(),
                'progress_to_next_card' => min(100, $monthlyLiters / SystemSetting::get('priority_card_liters_threshold', 500) * 100),
                'fuel_until_next_card' => max(0, SystemSetting::get('priority_card_liters_threshold', 500) - $monthlyLiters),
            ],
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        return response()->json(
            Refuel::query()->where('user_id', $request->user()->id)->latest('refuel_date')->get()
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $refuel = Refuel::query()->findOrFail($id);
        abort_if($refuel->user_id !== $request->user()->id && $request->user()->user_role !== 'admin', 403, 'Not authorized to view this record');

        return response()->json($refuel);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Admin only');

        $refuel = Refuel::query()->findOrFail($id);
        $refuel->delete();

        return response()->json([], 204);
    }
}
