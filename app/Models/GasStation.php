<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GasStation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'gas_stations';

    protected $fillable = [
        'owner_id',
        'station_name',
        'commercial_register',
        'location',
        'latitude',
        'longitude',
        'station_code',
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

    public function refuels()
    {
        return $this->hasMany(Refuel::class, 'station_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'station_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'station_id');
    }

    public function electronicCards()
    {
        return $this->hasMany(ElectronicCard::class, 'priority_station_id');
    }
}
