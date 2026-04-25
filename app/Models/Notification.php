<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'station_id',
        'car_wash_center_id',
        'maintenance_center_id',
        'sender_id',
        'title',
        'message',
        'notification_type',
        'is_read',
        'is_important',
        'created_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'is_important' => 'boolean',
            'created_at' => 'datetime',
            'read_at' => 'datetime',
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

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
