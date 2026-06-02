# Gas_Yemen_Laravel API

هذا الملف يلخص تغطية API الحالية بعد استكمال نقل معظم واجهات FastAPI الأساسية والمتقدمة إلى Laravel.

Base URL: `/api`

Authentication: `Sanctum` للواجهات المحمية.

System endpoints
- `GET /health`
- `GET /info`
- `GET /api-overview`
- `GET /schema`
- `GET /roles`
- `GET /services`

Authentication endpoints
- `POST /auth/register`
- `POST /auth/login`
- `POST /auth/verify`
- `POST /auth/verify-otp`
- `POST /auth/refresh-token`
- `POST /auth/logout`
- `GET /auth/me`
- `POST /auth/change-password`
- `POST /auth/enable-2fa`
- `POST /auth/disable-2fa`
- `GET /auth/status`

Analytics endpoints
- `GET /analytics/system-overview`
- `GET /analytics/user-growth`
- `GET /analytics/revenue-trends`
- `GET /analytics/subscription-analytics`

Stations and public services
- `GET /stations/nearby`
- `GET /stations`
- `GET /stations/{id}`
- `POST /stations`
- `PUT /stations/{id}`
- `DELETE /stations/{id}`
- `GET /car-washes`
- `GET /car-washes/nearby`
- `GET /maintenance-centers`
- `GET /maintenance-centers/nearby`
- `GET /search`

User endpoints
- `GET /user/profile`
- `PUT /user/profile`
- `GET /user/me`
- `PUT /user/me`
- `GET /user/stats`
- `GET /user/stations`
- `GET /user/stations/nearby`
- `GET /user/subscription`
- `GET /user/subscriptions/plans`
- `POST /user/subscriptions/subscribe`
- `GET /user/refuels`
- `GET /user/history/refuels`
- `GET /user/history/car-washes`
- `GET /user/history/maintenance`
- `GET /user/qr-codes`
- `POST /user/qr-codes/generate`
- `POST /user/tickets`
- `GET /user/notifications`

Subscriptions and refuels
- `GET /subscriptions/plans`
- `POST /subscriptions/subscribe`
- `POST /subscriptions/renew`
- `GET /subscriptions/my-status`
- `GET /subscriptions/fuel-status`
- `PUT /subscriptions/{id}/approve`
- `DELETE /subscriptions/{id}`
- `POST /refuels`
- `GET /refuels/fuel-analytics`
- `GET /refuels/history`

Owner endpoints
- `GET /owner/businesses`
- `GET /owner/overview`
- `GET /owner/my-stations`
- `GET /owner/my-car-washes`
- `GET /owner/my-maintenance-centers`
- `GET /owner/my-business`
- `GET /owner/my-employees`
- `POST /owner/employees`
- `GET /owner/reports/revenue`
- `PUT /owner/stations/{id}`
- `GET /owner/stations/{stationId}/today-refuels`
- `GET /owner/car-washes/{centerId}/today-washes`
- `GET /owner/maintenance/{centerId}/today-services`

Worker and compatibility service endpoints
- `GET /worker/overview`
- `GET /worker/my-workplace`
- `GET /worker/my-history/refuels`
- `GET /worker/my-history/car-washes`
- `GET /worker/my-history/maintenance`
- `GET /worker/my-stats`
- `POST /worker/services/refuels/validate-qr`
- `POST /worker/services/refuels/process`
- `POST /worker/services/car-washes/validate-qr`
- `POST /worker/services/car-washes/process`
- `POST /worker/services/maintenance/validate-qr`
- `POST /worker/services/maintenance/process`
- `POST /services/refuels/validate-qr`
- `POST /services/refuels/process`
- `GET /services/refuels/me`
- `POST /services/car-washes/validate-qr`
- `POST /services/car-washes/process`
- `GET /services/car-washes/me`
- `POST /services/maintenance/validate-qr`
- `POST /services/maintenance/process`
- `GET /services/maintenance/me`
- `GET /services/stations/{stationId}/today-refuels`
- `GET /services/stations/{stationId}/refuels/today`
- `GET /services/car-washes/{centerId}/today-washes`
- `GET /services/car-wash-centers/{centerId}/washes/today`
- `GET /services/maintenance/{centerId}/today-services`
- `GET /services/maintenance-centers/{centerId}/services/today`
- `GET /services/employees/{employeeId}/refuels`
- `GET /services/employees/{employeeId}/car-washes`
- `GET /services/employees/{employeeId}/maintenance`

Admin endpoints
- `GET /admin/users`
- `GET /admin/users/{userId}`
- `PUT /admin/users/{userId}/status`
- `POST /admin/users/{userId}/reset-password`
- `GET /admin/stations`
- `POST /admin/stations`
- `DELETE /admin/stations/{id}`
- `GET /admin/car-wash-centers`
- `POST /admin/car-wash-centers`
- `DELETE /admin/car-wash-centers/{id}`
- `GET /admin/maintenance-centers`
- `POST /admin/maintenance-centers`
- `DELETE /admin/maintenance-centers/{id}`
- `PUT /admin/businesses/{businessId}/approve`
- `PUT /admin/businesses/{businessId}/suspend`
- `GET /admin/transactions`
- `GET /admin/revenue/summary`
- `GET /admin/analytics/user-behavior`
- `GET /admin/system/health`
- `POST /admin/system/maintenance`
- `GET /admin/security/logs`
- `GET /admin/security/threats`
- `POST /admin/security/lock-user`
- `GET /admin/reports/daily-activity`
- `GET /admin/reports/daily`
- `GET /admin/reports/export`
- `PUT /admin/subscriptions/{id}/approve`
- `GET /admin/tickets`
- `PUT /admin/tickets/{ticketId}`
- `POST /admin/broadcast`

Tickets endpoints
- `POST /tickets`
- `GET /tickets/me`
- `GET /tickets`
- `PUT /tickets/{ticketId}`

Electronic cards endpoints
- `GET /electronic-cards/my-cards`
- `GET /electronic-cards/priority-status`
- `GET /electronic-cards/validate/{cardNumber}`
- `GET /electronic-cards/admin/all-cards`
- `GET /electronic-cards/admin/user-cards/{userId}`
- `GET /electronic-cards/{cardNumber}`
- `POST /electronic-cards/{cardNumber}/use-priority`

للتحقق السريع: `php artisan route:list --path=api`
