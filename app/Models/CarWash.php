<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarWash extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'car_washes';

    protected $fillable = [
        'user_id',
        'center_id',
        'subscription_id',
        'employee_id',
        'wash_type',
        'qr_code_used',
        'wash_date',
        'completed_at',
        'notes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'wash_date' => 'datetime',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function center()
    {
        return $this->belongsTo(CarWashCenter::class, 'center_id');
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
