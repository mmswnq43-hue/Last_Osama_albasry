<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ElectronicCardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\PublicServiceController;
use App\Http\Controllers\Api\RefuelController;
use App\Http\Controllers\Api\StationController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'message' => 'مرحباً بك في نظام غازي المتكامل v2.1',
        'version' => '2.1.0',
        'roles_supported' => ['admin', 'owner', 'worker', 'customer'],
    ];
});

Route::get('/health', [SystemController::class, 'health']);
Route::get('/info', [SystemController::class, 'info']);
Route::get('/api-overview', [SystemController::class, 'apiOverview']);
Route::get('/schema', [SystemController::class, 'schema']);
Route::get('/roles', [SystemController::class, 'roles']);
Route::get('/services', [SystemController::class, 'services']);

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify', [AuthController::class, 'verifyOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/enable-2fa', [AuthController::class, 'enableTwoFactor']);
        Route::post('/disable-2fa', [AuthController::class, 'disableTwoFactor']);
        Route::get('/status', [AuthController::class, 'status']);
    });
});

Route::prefix('analytics')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/system-overview', [AnalyticsController::class, 'systemOverview']);
    Route::get('/user-growth', [AnalyticsController::class, 'userGrowth']);
    Route::get('/revenue-trends', [AnalyticsController::class, 'revenueTrends']);
    Route::get('/subscription-analytics', [AnalyticsController::class, 'subscriptionAnalytics']);
});

Route::get('/stations/nearby', [StationController::class, 'nearby']);
Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/stations', [StationController::class, 'index']);
    Route::get('/stations/{id}', [StationController::class, 'show']);
    Route::post('/stations', [StationController::class, 'store']);
    Route::put('/stations/{id}', [StationController::class, 'update']);
    Route::delete('/stations/{id}', [StationController::class, 'destroy']);
});

Route::get('/car-washes/nearby', [PublicServiceController::class, 'nearbyCarWashes']);
Route::get('/car-washes', [PublicServiceController::class, 'carWashes']);
Route::get('/car-washes/{id}', [PublicServiceController::class, 'carWashDetails']);
Route::get('/maintenance-centers/nearby', [PublicServiceController::class, 'nearbyMaintenanceCenters']);
Route::get('/maintenance-centers', [PublicServiceController::class, 'maintenanceCenters']);
Route::get('/maintenance-centers/{id}', [PublicServiceController::class, 'maintenanceCenterDetails']);
Route::get('/search', [PublicServiceController::class, 'search']);

Route::prefix('subscriptions')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/plans', [SubscriptionController::class, 'plans']);
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/renew', [SubscriptionController::class, 'renew']);
    Route::get('/my-status', [SubscriptionController::class, 'myStatus']);
    Route::get('/fuel-status', [SubscriptionController::class, 'fuelStatus']);
    Route::put('/{id}/approve', [SubscriptionController::class, 'approve']);
    Route::delete('/{id}', [SubscriptionController::class, 'destroy']);
});

Route::prefix('refuels')->middleware('auth:sanctum')->group(function (): void {
    Route::post('/', [RefuelController::class, 'store']);
    Route::get('/fuel-analytics', [RefuelController::class, 'fuelAnalytics']);
    Route::get('/history', [RefuelController::class, 'history']);
    Route::get('/{id}', [RefuelController::class, 'show']);
    Route::delete('/{id}', [RefuelController::class, 'destroy']);
});

Route::prefix('notifications')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/check-automatic', [NotificationController::class, 'checkAutomatic']);
    Route::get('/', [NotificationController::class, 'index']);
    Route::post('/broadcast', [NotificationController::class, 'broadcast']);
    Route::put('/{id}/read', [NotificationController::class, 'markRead']);
});

Route::prefix('electronic-cards')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/my-cards', [ElectronicCardController::class, 'myCards']);
    Route::get('/priority-status', [ElectronicCardController::class, 'priorityStatus']);
    Route::get('/validate/{cardNumber}', [ElectronicCardController::class, 'validateCard']);
    Route::get('/admin/all-cards', [ElectronicCardController::class, 'allCards']);
    Route::get('/admin/user-cards/{userId}', [ElectronicCardController::class, 'userCards']);
    Route::get('/{cardNumber}', [ElectronicCardController::class, 'show']);
    Route::post('/{cardNumber}/use-priority', [ElectronicCardController::class, 'usePriority']);
});

Route::prefix('user')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/profile', [UserController::class, 'me']);
    Route::put('/profile', [UserController::class, 'updateMe']);
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/me', [UserController::class, 'updateMe']);
    Route::get('/stats', [UserController::class, 'stats']);
    Route::get('/stations', [UserController::class, 'stations']);
    Route::get('/stations/nearby', [UserController::class, 'nearbyStations']);
    Route::get('/subscriptions/plans', [UserController::class, 'plans']);
    Route::post('/subscriptions/subscribe', [UserController::class, 'subscribe']);
    Route::get('/subscription', [SubscriptionController::class, 'activeStatus']);
    Route::get('/history/refuels', [UserController::class, 'refuels']);
    Route::get('/refuels', [UserController::class, 'refuels']);
    Route::get('/history/car-washes', [UserController::class, 'carWashes']);
    Route::get('/history/maintenance', [UserController::class, 'maintenance']);
    Route::get('/qr-codes', [UserController::class, 'qrCodes']);
    Route::post('/qr-codes/generate', [UserController::class, 'generateQrCode']);
    Route::post('/stations/rate', [UserController::class, 'rateStation']);
    Route::get('/payments/methods', [UserController::class, 'paymentMethods']);
    Route::post('/payments/process', [UserController::class, 'processPayment']);
    Route::post('/tickets', [UserController::class, 'createTicket']);
    Route::get('/notifications', [UserController::class, 'notifications']);
    Route::get('/{userId}', [UserController::class, 'show']);
});

Route::prefix('owner')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/businesses', [OwnerController::class, 'myBusiness']);
    Route::get('/overview', [OwnerController::class, 'myBusiness']);
    Route::get('/my-stations', [OwnerController::class, 'myStations']);
    Route::get('/my-car-washes', [OwnerController::class, 'myCarWashes']);
    Route::get('/my-maintenance-centers', [OwnerController::class, 'myMaintenanceCenters']);
    Route::get('/my-business', [OwnerController::class, 'myBusiness']);
    Route::get('/my-employees', [OwnerController::class, 'myEmployees']);
    Route::post('/employees', [OwnerController::class, 'addEmployee']);
    Route::get('/reports/revenue', [OwnerController::class, 'revenue']);
    Route::put('/stations/{id}', [OwnerController::class, 'updateStation']);
    Route::get('/stations/{stationId}/today-refuels', [OwnerController::class, 'stationTodayRefuels']);
    Route::get('/car-washes/{centerId}/today-washes', [OwnerController::class, 'washCenterToday']);
    Route::get('/maintenance/{centerId}/today-services', [OwnerController::class, 'maintenanceToday']);
});

Route::prefix('admin')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/users', [AdminController::class, 'createUser']);
    Route::get('/users/pending', [AdminController::class, 'pendingUsers']);
    Route::get('/users/{userId}', [AdminController::class, 'userDetails']);
    Route::put('/users/{userId}/status', [AdminController::class, 'updateUserStatus']);
    Route::put('/users/{userId}/role', [AdminController::class, 'updateUserRole']);
    Route::put('/users/{userId}/approve', [AdminController::class, 'approveUser']);
    Route::put('/users/{userId}/reject', [AdminController::class, 'rejectUser']);
    Route::post('/users/{userId}/reset-password', [AdminController::class, 'resetUserPassword']);
    Route::get('/stations', [AdminController::class, 'stations']);
    Route::post('/stations', [AdminController::class, 'createStation']);
    Route::put('/stations/{id}', [AdminController::class, 'updateStation']);
    Route::delete('/stations/{id}', [AdminController::class, 'deleteStation']);
    Route::get('/car-wash-centers', [AdminController::class, 'carWashCenters']);
    Route::post('/car-wash-centers', [AdminController::class, 'createCarWashCenter']);
    Route::put('/car-wash-centers/{id}', [AdminController::class, 'updateCarWashCenter']);
    Route::delete('/car-wash-centers/{id}', [AdminController::class, 'deleteCarWashCenter']);
    Route::get('/maintenance-centers', [AdminController::class, 'maintenanceCenters']);
    Route::post('/maintenance-centers', [AdminController::class, 'createMaintenanceCenter']);
    Route::put('/maintenance-centers/{id}', [AdminController::class, 'updateMaintenanceCenter']);
    Route::delete('/maintenance-centers/{id}', [AdminController::class, 'deleteMaintenanceCenter']);
    Route::put('/businesses/{businessId}/approve', [AdminController::class, 'approveBusiness']);
    Route::put('/businesses/{businessId}/suspend', [AdminController::class, 'suspendBusiness']);
    Route::get('/subscriptions', [AdminController::class, 'allSubscriptions']);
    Route::get('/transactions', [AdminController::class, 'transactions']);
    Route::get('/revenue/summary', [AdminController::class, 'revenueSummary']);
    Route::get('/analytics/user-behavior', [AdminController::class, 'userBehaviorAnalytics']);
    Route::get('/system/health', [AdminController::class, 'systemHealth']);
    Route::post('/system/maintenance', [AdminController::class, 'scheduleMaintenance']);
    Route::get('/security/logs', [AdminController::class, 'securityLogs']);
    Route::get('/security/threats', [AdminController::class, 'securityThreats']);
    Route::post('/security/lock-user', [AdminController::class, 'lockUser']);
    Route::get('/reports/daily-activity', [AdminController::class, 'dailyActivity']);
    Route::get('/reports/daily', [AdminController::class, 'dailyActivity']);
    Route::get('/reports/export', [AdminController::class, 'exportReport']);
    Route::put('/subscriptions/{id}/approve', [AdminController::class, 'approveSubscription']);
    Route::get('/tickets', [AdminController::class, 'tickets']);
    Route::put('/tickets/{ticketId}', [AdminController::class, 'updateTicket']);
    Route::post('/broadcast', [NotificationController::class, 'broadcast']);
});

Route::prefix('worker')->middleware('auth:sanctum')->group(function (): void {
    Route::get('/overview', [WorkerController::class, 'myStats']);
    Route::get('/my-workplace', [WorkerController::class, 'myWorkplace']);
    Route::get('/my-history/refuels', [WorkerController::class, 'myRefuelHistory']);
    Route::get('/my-history/car-washes', [WorkerController::class, 'myCarWashHistory']);
    Route::get('/my-history/maintenance', [WorkerController::class, 'myMaintenanceHistory']);
    Route::get('/my-stats', [WorkerController::class, 'myStats']);
    Route::post('/services/refuels/validate-qr', [WorkerController::class, 'validateRefuelQr']);
    Route::post('/services/refuels/process', [WorkerController::class, 'processRefuel']);
    Route::post('/services/car-washes/process', [WorkerController::class, 'processCarWash']);
    Route::post('/services/car-washes/validate-qr', [WorkerController::class, 'validateCarWashQr']);
    Route::post('/services/maintenance/validate-qr', [WorkerController::class, 'validateMaintenanceQr']);
    Route::post('/services/maintenance/process', [WorkerController::class, 'processMaintenance']);
});

Route::prefix('tickets')->middleware('auth:sanctum')->group(function (): void {
    Route::post('/', [TicketController::class, 'create']);
    Route::get('/me', [TicketController::class, 'myTickets']);
    Route::get('/', [TicketController::class, 'index']);
    Route::put('/{ticketId}', [TicketController::class, 'update']);
});

Route::prefix('services')->middleware('auth:sanctum')->group(function (): void {
    Route::post('/refuels/validate-qr', [WorkerController::class, 'validateRefuelQr']);
    Route::post('/refuels/process', [WorkerController::class, 'processRefuel']);
    Route::get('/refuels/me', [UserController::class, 'refuels']);
    Route::post('/car-washes/validate-qr', [WorkerController::class, 'validateCarWashQr']);
    Route::post('/car-washes/process', [WorkerController::class, 'processCarWash']);
    Route::get('/car-washes/me', [UserController::class, 'carWashes']);
    Route::post('/maintenance/validate-qr', [WorkerController::class, 'validateMaintenanceQr']);
    Route::post('/maintenance/process', [WorkerController::class, 'processMaintenance']);
    Route::get('/maintenance/me', [UserController::class, 'maintenance']);
    Route::get('/stations/{stationId}/today-refuels', [OwnerController::class, 'stationTodayRefuels']);
    Route::get('/stations/{stationId}/refuels/today', [OwnerController::class, 'stationTodayRefuels']);
    Route::get('/car-washes/{centerId}/today-washes', [OwnerController::class, 'washCenterToday']);
    Route::get('/car-wash-centers/{centerId}/washes/today', [OwnerController::class, 'washCenterToday']);
    Route::get('/maintenance/{centerId}/today-services', [OwnerController::class, 'maintenanceToday']);
    Route::get('/maintenance-centers/{centerId}/services/today', [OwnerController::class, 'maintenanceToday']);
    Route::get('/employees/{employeeId}/refuels', [WorkerController::class, 'employeeRefuels']);
    Route::get('/employees/{employeeId}/car-washes', [WorkerController::class, 'employeeCarWashes']);
    Route::get('/employees/{employeeId}/maintenance', [WorkerController::class, 'employeeMaintenance']);
});
