<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'station_id',
        'car_wash_center_id',
        'maintenance_center_id',
        'employee_code',
        'position',
        'hire_date',
        'salary',
        'is_active',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'datetime',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
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

    public function carWashCenter()
    {
        return $this->belongsTo(CarWashCenter::class, 'car_wash_center_id');
    }

    public function maintenanceCenter()
    {
        return $this->belongsTo(MaintenanceCenter::class, 'maintenance_center_id');
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
