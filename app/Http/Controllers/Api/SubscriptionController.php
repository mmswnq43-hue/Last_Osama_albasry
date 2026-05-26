<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ElectronicCard;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Services\GasYemenNotificationService;
use App\Services\GasYemenSubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly GasYemenSubscriptionService $subscriptions,
        private readonly GasYemenNotificationService $notifications,
    ) {
    }

    public function plans(): JsonResponse
    {
        return response()->json(array_values($this->subscriptions->plans()));
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan_type' => ['required', 'string'],
            'price' => ['nullable', 'numeric'],
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'local');
        }

        $subscription = $this->subscriptions->createSubscription(
            $request->user(),
            $data['plan_type'],
            'pending_payment',
            $receiptPath,
        );

        return response()->json($subscription, 201);
    }

    public function myStatus(Request $request): JsonResponse
    {
        $subscriptions = Subscription::query()
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->get();

        return response()->json($subscriptions);
    }

    public function activeStatus(Request $request): JsonResponse
    {
        $subscription = $this->subscriptions->activeSubscription($request->user());
        abort_if(! $subscription, 404, 'No active subscription found');

        return response()->json($subscription);
    }

    public function fuelStatus(Request $request): JsonResponse
    {
        $subscription = $this->subscriptions->activeSubscription($request->user());
        abort_if(! $subscription, 404, 'No active subscription found');

        $priorityCards = ElectronicCard::query()
            ->where('user_id', $request->user()->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->count();

        $monthlyLiters = (float) $subscription->monthly_liters;

        return response()->json([
            'subscription_id' => $subscription->id,
            'plan_type' => $subscription->plan_type,
            'monthly_fuel_used' => $monthlyLiters,
            'fuel_until_priority_card' => max(0, SystemSetting::get('priority_card_liters_threshold', 500) - $monthlyLiters),
            'progress_to_priority_card' => min(100, $monthlyLiters / SystemSetting::get('priority_card_liters_threshold', 500) * 100),
            'active_priority_cards' => $priorityCards,
            'last_monthly_reset' => $subscription->last_reset_date,
            'end_date' => $subscription->end_date,
        ]);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Only admins can approve subscriptions');

        $subscription = Subscription::query()->findOrFail($id);
        $subscription->forceFill(['status' => 'active'])->save();

        $this->notifications->createSystemNotification(
            $subscription->user_id,
            'تم تفعيل اشتراكك!',
            'مبروك! تم تفعيل اشتراكك بنجاح.',
            'subscription_activated',
        );

        return response()->json($subscription->refresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Admin only');

        $subscription = Subscription::query()->findOrFail($id);
        $subscription->delete();

        return response()->json([], 204);
    }

    public function renew(Request $request): JsonResponse
    {
        $data = $request->validate([
            'plan_type' => ['required', 'string'],
        ]);

        $subscription = $this->subscriptions->createSubscription($request->user(), $data['plan_type'], 'active');

        return response()->json([
            'subscription_id' => $subscription->id,
            'plan_type' => $subscription->plan_type,
            'status' => $subscription->status,
            'remaining_car_washes' => $subscription->remaining_car_washes,
            'end_date' => $subscription->end_date,
        ], 201);
    }
}
