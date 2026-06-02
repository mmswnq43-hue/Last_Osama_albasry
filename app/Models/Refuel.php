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
        'status',
        'fuel_type',
        'qr_code_used',
        'refuel_date',
        'created_at',
    ];

    // eager load station مع كل refuel تلقائياً
    protected $with = ['station'];

    // حقول محسوبة للتوافق مع Flutter
    protected $appends = ['station_name', 'amount', 'date'];

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

    // Accessor: station_name ← متوافق مع Flutter
    public function getStationNameAttribute(): string
    {
        return $this->station?->station_name ?? '';
    }

    // Accessor: amount ← Flutter يتوقع "amount" بدلاً من "final_price"
    public function getAmountAttribute(): float
    {
        return (float) $this->final_price;
    }

    // Accessor: date ← Flutter يتوقع "date" بدلاً من "refuel_date"
    public function getDateAttribute(): ?string
    {
        return $this->refuel_date?->toIso8601String();
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
