<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use App\Services\GasYemenNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly GasYemenNotificationService $notifications,
    ) {
    }

    public function checkAutomatic(Request $request): JsonResponse
    {
        abort_unless($request->user()->user_role === 'admin', 403, 'Admin only');

        $expiringSoon = Subscription::query()
            ->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->copy()->addDays(3)])
            ->get();

        foreach ($expiringSoon as $subscription) {
            $this->notifications->createSystemNotification(
                $subscription->user_id,
                'تنبيه انتهاء الاشتراك',
                'ينتهي اشتراكك خلال 3 أيام! جدد الآن.',
                'subscription_expiring',
            );
        }

        return response()->json(['status' => 'Automatic notifications triggered']);
    }

    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::query()
            ->where(function ($query) use ($request): void {
                $query->where('user_id', $request->user()->id)
                    ->orWhereNull('user_id');
            })
            ->latest('created_at')
            ->get();

        return response()->json($notifications);
    }

    public function broadcast(Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->user_role, ['admin', 'station_owner'], true), 403, 'Not authorized to send notifications');

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'notification_type' => ['nullable', 'string', 'max:50'],
            'target_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'station_id' => ['nullable', 'integer', 'exists:gas_stations,id'],
            'is_important' => ['nullable', 'boolean'],
            'target_audience' => ['nullable', 'string', 'max:50'],
            'channels' => ['nullable', 'array'],
            'channels.*' => ['string'],
            'priority' => ['nullable', 'string', 'max:20'],
            'scheduled_for' => ['nullable', 'date'],
        ]);

        $title = $data['title'] ?? 'إعلان عام';
        $notificationType = $data['notification_type'] ?? 'system_alert';
        $isImportant = (bool) ($data['is_important'] ?? (($data['priority'] ?? 'normal') === 'high'));
        $targetUserIds = collect();

        if (! empty($data['target_user_id'])) {
            $targetUserIds->push((int) $data['target_user_id']);
        } elseif (! empty($data['target_audience'])) {
            $targetUserIds = match ($data['target_audience']) {
                'all_users' => User::query()->where('is_active', true)->pluck('id'),
                'station_owners' => User::query()->where('user_role', 'station_owner')->pluck('id'),
                'car_wash_owners' => User::query()->where('user_role', 'car_wash_owner')->pluck('id'),
                'maintenance_owners' => User::query()->where('user_role', 'maintenance_owner')->pluck('id'),
                default => collect(),
            };
        }

        if ($targetUserIds->isEmpty()) {
            $notification = Notification::create([
                'user_id' => null,
                'station_id' => $data['station_id'] ?? null,
                'sender_id' => $request->user()->id,
                'title' => $title,
                'message' => $data['message'],
                'notification_type' => $notificationType,
                'is_important' => $isImportant,
            ]);

            return response()->json(['status' => 'success', 'notification_id' => $notification->id, 'notifications_sent' => 1]);
        }

        $sent = 0;
        foreach ($targetUserIds->unique() as $userId) {
            Notification::create([
                'user_id' => $userId,
                'station_id' => $data['station_id'] ?? null,
                'sender_id' => $request->user()->id,
                'title' => $title,
                'message' => $data['message'],
                'notification_type' => $notificationType,
                'is_important' => $isImportant,
            ]);
            $sent++;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال الرسالة بنجاح',
            'notifications_sent' => $sent,
            'target_audience' => $data['target_audience'] ?? null,
            'channels' => $data['channels'] ?? [],
            'scheduled_for' => $data['scheduled_for'] ?? null,
        ]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $notification->forceFill([
            'is_read' => true,
            'read_at' => now(),
        ])->save();

        return response()->json(['status' => 'success']);
    }
}
