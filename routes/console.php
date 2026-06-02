<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:run')->weekly();
Schedule::command('backup:clean')->weekly();

// إشعار قبل انتهاء الاشتراك بـ 7 أيام — يعمل كل يوم الساعة 9 صباحاً
Schedule::command('subscriptions:notify-expiring')->dailyAt('09:00');

// تفعيل الاشتراكات المجدولة — يعمل كل ساعة
Schedule::command('subscriptions:activate-scheduled')->hourly();
