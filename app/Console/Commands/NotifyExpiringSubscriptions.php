<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Subscription;
use Illuminate\Console\Command;

class NotifyExpiringSubscriptions extends Command
{
    protected $signature   = 'subscriptions:notify-expiring';
    protected $description = 'إرسال إشعار للعملاء الذين اشتراكهم سينتهي خلال 7 أيام';

    public function handle(): void
    {
        $expiring = Subscription::where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->with('user')
            ->get();

        foreach ($expiring as $sub) {
            if (! $sub->user) continue;

            $days = now()->diffInDays($sub->end_date);

            // تجنب التكرار: لا ترسل إشعاراً إذا أُرسل في آخر 24 ساعة
            $alreadySent = Notification::where('user_id', $sub->user_id)
                ->where('notification_type', 'subscription_expiring')
                ->where('created_at', '>=', now()->subHours(24))
                ->exists();

            if ($alreadySent) continue;

            Notification::create([
                'user_id'           => $sub->user_id,
                'title'             => 'اشتراكك سينتهي قريباً',
                'message'           => "اشتراكك في باقة {$sub->plan_type} سينتهي خلال {$days} " . ($days === 1 ? 'يوم' : 'أيام') . '. يمكنك تجديده من صفحة اشتراكاتك.',
                'notification_type' => 'subscription_expiring',
                'is_important'      => true,
            ]);
        }

        $this->info("تم إرسال إشعارات لـ {$expiring->count()} اشتراك.");
    }
}
