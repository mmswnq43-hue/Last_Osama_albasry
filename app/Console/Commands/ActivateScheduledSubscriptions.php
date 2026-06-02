<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ActivateScheduledSubscriptions extends Command
{
    protected $signature   = 'subscriptions:activate-scheduled';
    protected $description = 'تفعيل الاشتراكات المجدولة التي حان وقت بدئها';

    public function handle(): void
    {
        $due = Subscription::where('status', 'scheduled')
            ->where('start_date', '<=', now())
            ->with('user')
            ->get();

        foreach ($due as $sub) {
            $sub->update(['status' => 'active']);

            if ($sub->user) {
                Notification::create([
                    'user_id'           => $sub->user_id,
                    'title'             => 'تم تفعيل اشتراكك الجديد',
                    'message'           => "تم تفعيل اشتراكك في باقة {$sub->plan_type} تلقائياً. استمتع بالخدمة!",
                    'notification_type' => 'subscription_activated',
                    'is_important'      => true,
                ]);
            }
        }

        $this->info("تم تفعيل {$due->count()} اشتراك مجدوَل.");
    }
}
