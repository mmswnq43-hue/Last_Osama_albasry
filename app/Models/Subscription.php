<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_type',
        'price',
        'discount_percent',
        'start_date',
        'end_date',
        'status',
        'payment_receipt_image',
        'remaining_cylinders',
        'remaining_car_washes',
        'remaining_maintenance',
        'notes',
        'monthly_liters',
        'last_reset_date',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'integer',
            'remaining_cylinders' => 'integer',
            'remaining_car_washes' => 'integer',
            'remaining_maintenance' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'monthly_liters' => 'decimal:2',
            'last_reset_date' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function refuels()
    {
        return $this->hasMany(Refuel::class);
    }

    public function carWashes()
    {
        return $this->hasMany(CarWash::class);
    }

    public function maintenanceServices()
    {
        return $this->hasMany(MaintenanceService::class);
    }
}
