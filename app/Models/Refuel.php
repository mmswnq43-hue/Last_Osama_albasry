<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refuel extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'station_id',
        'subscription_id',
        'employee_id',
        'liters',
        'price_per_liter',
        'total_before_discount',
        'discount_amount',
        'final_price',
        'qr_code_used',
        'refuel_date',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'liters' => 'decimal:2',
            'price_per_liter' => 'decimal:2',
            'total_before_discount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_price' => 'decimal:2',
            'refuel_date' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function station()
    {
        return $this->belongsTo(GasStation::class, 'station_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
