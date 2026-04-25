<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarWashCenter extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'car_wash_centers';

    protected $fillable = [
        'owner_id',
        'center_name',
        'commercial_register',
        'location',
        'latitude',
        'longitude',
        'center_code',
        'is_active',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function carWashes()
    {
        return $this->hasMany(CarWash::class, 'center_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'car_wash_center_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'car_wash_center_id');
    }

    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
