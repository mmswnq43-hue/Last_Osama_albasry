<?php

namespace App\Services;

use App\Models\Notification;

class GasYemenNotificationService
{
    public function createSystemNotification(
        int $userId,
        string $title,
        string $message,
        string $notificationType = 'system_alert',
        ?int $senderId = null,
        ?int $stationId = null,
        ?int $carWashCenterId = null,
        ?int $maintenanceCenterId = null,
        bool $important = false,
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'station_id' => $stationId,
            'car_wash_center_id' => $carWashCenterId,
            'maintenance_center_id' => $maintenanceCenterId,
            'title' => $title,
            'message' => $message,
            'notification_type' => $notificationType,
            'is_important' => $important,
        ]);
    }
}
