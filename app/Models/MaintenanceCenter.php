<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceCenter extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'maintenance_centers';

    protected $fillable = [
        'owner_id',
        'center_name',
        'commercial_register',
        'location',
        'latitude',
        'longitude',
        'center_code',
        'specialization',
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

    public function maintenanceServices()
    {
        return $this->hasMany(MaintenanceService::class, 'center_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'maintenance_center_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'maintenance_center_id');
    }

    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
