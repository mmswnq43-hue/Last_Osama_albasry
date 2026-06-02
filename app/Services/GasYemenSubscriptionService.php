<?php

namespace App\Services;

use App\Models\ElectronicCard;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GasYemenSubscriptionService
{
    public function __construct(
        private readonly GasYemenNotificationService $notifications,
    ) {
    }

    public function plans(): array
    {
        return [
            'monthly' => [
                'plan_type' => 'monthly',
                'price' => 20.00,
                'duration_months' => 1,
                'car_washes' => 0,
                'maintenance' => 0,
                'discount_percent' => 0,
                'description' => 'اشتراك شهري - كرت أولوية شهري عند كل 500 لتر',
            ],
            '3months' => [
                'plan_type' => '3months',
                'price' => 45.00,
                'duration_months' => 3,
                'car_washes' => 1,
                'maintenance' => 0,
                'discount_percent' => 0,
                'description' => 'اشتراك 3 أشهر - كرت أولوية شهري + غسيل سيارة مرة واحدة',
            ],
            '6months' => [
                'plan_type' => '6months',
                'price' => 100.00,
                'duration_months' => 6,
                'car_washes' => 3,
                'maintenance' => 1,
                'discount_percent' => 0,
                'description' => 'اشتراك 6 أشهر - كرت أولوية شهري + غسيل كل شهرين + صيانة دورية',
            ],
            'yearly' => [
                'plan_type' => 'yearly',
                'price' => 195.00,
                'duration_months' => 12,
                'car_washes' => 6,
                'maintenance' => 1,
                'discount_percent' => 0,
                'description' => 'اشتراك سنوي - كرت أولوية شهري + غسيل كل شهرين + صيانة سنوية',
            ],
        ];
    }

    public function activeSubscription(User $user): ?Subscription
    {
        $subscription = Subscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest('end_date')
            ->first();

        if ($subscription) {
            $this->resetMonthlyLiters($subscription);
        }

        return $subscription;
    }

    public function resetMonthlyLiters(Subscription $subscription): Subscription
    {
        $now = now();

        if (! $subscription->last_reset_date
            || $subscription->last_reset_date->month !== $now->month
            || $subscription->last_reset_date->year !== $now->year) {
            $subscription->forceFill([
                'monthly_liters' => 0,
                'last_reset_date' => $now,
            ])->save();
        }

        return $subscription->refresh();
    }

    public function createSubscription(User $user, string $planType, string $status = 'pending', ?string $receiptPath = null): Subscription
    {
        $plans = $this->plans();
        abort_unless(isset($plans[$planType]), 400, 'Invalid plan type');

        $plan = $plans[$planType];
        $startDate = now();

        return Subscription::create([
            'user_id' => $user->id,
            'plan_type' => $planType,
            'price' => $plan['price'],
            'discount_percent' => $plan['discount_percent'],
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addMonths($plan['duration_months']),
            'status' => $status,
            'payment_receipt_image' => $receiptPath,
            'remaining_cylinders' => 0,
            'remaining_car_washes' => $plan['car_washes'],
            'remaining_maintenance' => $plan['maintenance'],
            'notes' => 'User requested '.$planType.' plan',
            'monthly_liters' => 0,
            'last_reset_date' => $startDate,
        ]);
    }

    public function addMonthlyLiters(Subscription $subscription, float $liters): Subscription
    {
        $subscription = $this->resetMonthlyLiters($subscription);
        $subscription->monthly_liters = (float) $subscription->monthly_liters + $liters;
        $subscription->save();

        return $subscription->refresh();
    }

    public function generatePriorityCardIfEligible(Subscription $subscription): ?ElectronicCard
    {
        $subscription = $this->resetMonthlyLiters($subscription);

        $threshold = SystemSetting::get('priority_card_liters_threshold', 500);
        if ((float) $subscription->monthly_liters < $threshold) {
            return null;
        }

        $existingCard = ElectronicCard::query()
            ->where('user_id', $subscription->user_id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingCard) {
            return null;
        }

        $validityDays = SystemSetting::get('priority_card_validity_days', 30);
        $expiresAt = Carbon::now()->addDays($validityDays)->setTime(23, 59, 59);

        $card = ElectronicCard::create([
            'user_id' => $subscription->user_id,
            'card_number' => 'EC'.strtoupper(Str::random(10)),
            'monthly_liters_at_generation' => $subscription->monthly_liters,
            'expires_at' => $expiresAt,
            'generated_at' => now(),
        ]);

        $this->notifications->createSystemNotification(
            $subscription->user_id,
            'كرت أولوية التعبئة',
            'تم توليد كرت أولوية جديد بعد تجاوز '.$threshold.' لتر هذا الشهر.',
            'priority_card_generated',
        );

        return $card;
    }

    public function qrPayload(User $user, Subscription $subscription, ?string $serviceType = null): array
    {
        $expiresAt = now()->addMinutes(5);
        $payload = [];

        if (! $serviceType || $serviceType === 'refuel') {
            $payload['refuel_qr'] = 'GS-REFUEL-'.$user->id.'-'.Str::upper(Str::random(12));
            $payload['refuel_expires_at'] = $expiresAt;
        }

        if (! $serviceType || $serviceType === 'car_wash') {
            if ($subscription->remaining_car_washes > 0) {
                $payload['car_wash_qr'] = 'GS-WASH-'.$user->id.'-'.Str::upper(Str::random(12));
                $payload['car_wash_expires_at'] = $expiresAt;
            } else {
                $payload['car_wash_qr'] = null;
                $payload['car_wash_message'] = 'No remaining car washes';
            }
        }

        if (! $serviceType || $serviceType === 'maintenance') {
            if ($subscription->remaining_maintenance > 0) {
                $payload['maintenance_qr'] = 'GS-MAIN-'.$user->id.'-'.Str::upper(Str::random(12));
                $payload['maintenance_expires_at'] = $expiresAt;
            } else {
                $payload['maintenance_qr'] = null;
                $payload['maintenance_message'] = 'No remaining maintenance services';
            }
        }

        return [
            'user_id' => $user->id,
            'qr_codes' => $payload,
            'expires_at' => $expiresAt,
            'subscription_info' => [
                'remaining_car_washes' => $subscription->remaining_car_washes,
                'remaining_maintenance' => $subscription->remaining_maintenance,
            ],
        ];
    }
}
