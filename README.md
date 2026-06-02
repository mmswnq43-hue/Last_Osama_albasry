# Gas Yemen Laravel

هذا المشروع هو تحويل بنية قاعدة البيانات الخاصة بمشروع Gas_Yemen من Python/FastAPI/SQLAlchemy إلى Laravel 12 مع ربط فعلي بقاعدة بيانات MySQL.

## ما تم تنفيذه

- إنشاء مشروع Laravel جديد باسم Gas_Yemen_Laravel.
- تحويل مخطط قاعدة البيانات الأساسي من ملف models.py في مشروع Gas_Yemen إلى Laravel migration.
- إنشاء نماذج Eloquent للجداول الأساسية.
- ضبط الاتصال الافتراضي على MySQL داخل ملفات البيئة.
- إنشاء قاعدة بيانات MySQL باسم gas_yemen_laravel.
- تنفيذ migrations بنجاح على MySQL.

## الجداول التي تم إنشاؤها

- users
- gas_stations
- car_wash_centers
- maintenance_centers
- employees
- subscriptions
- refuels
- car_washes
- maintenance_services
- notifications
- security_logs
- support_tickets
- electronic_cards

## إعداد قاعدة البيانات

الإعداد الحالي داخل .env:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gas_yemen_laravel
DB_USERNAME=root
DB_PASSWORD=
```

## أوامر التشغيل

```bash
composer install
php artisan migrate
php artisan serve
```

## ملاحظات مهمة

- تم الحفاظ على أسماء الجداول والعلاقات الأساسية لتكون قريبة من مشروع Gas_Yemen الأصلي.
- تم استخدام enum في بعض الحقول المقيدة مثل user_role و status و priority بدل قيود SQLAlchemy المباشرة.
- جدول migrations خاص بـ Laravel وتمت إضافته تلقائيًا لتتبع حالة الهجرات.
- المشروع الحالي يركز على طبقة البيانات والنماذج. لم يتم بعد نقل جميع Routes وControllers وBusiness Logic من مشروع FastAPI.

## الملفات المهمة

- app/Models
- database/migrations/0001_01_01_000000_create_users_table.php
- .env

## الخطوة التالية المقترحة

إذا أردت، يمكن توسيع المشروع في المرحلة التالية ليشمل:

1. Controllers وAPI Resources مطابقة لـ FastAPI.
2. Auth باستخدام Laravel Sanctum أو JWT.
3. Seeders وFactories لبيانات تجريبية.
4. لوحات إدارة وواجهات Blade أو API فقط.
# bacend_laravel_gas
