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
        'is_open',
        'phone',
        'rating',
        'rating_count',
        'services',
        'created_at',
        'city',
        'district',
        'license_number',
        'license_issue_date',
        'license_expiry_date',
        'pumps_count',
        'fuel_types',
    ];

    // حقول محسوبة للتوافق مع Flutter
    protected $appends = ['name', 'address'];

    protected function casts(): array
    {
        return [
            'latitude'             => 'decimal:8',
            'longitude'            => 'decimal:8',
            'is_active'            => 'boolean',
            'is_open'              => 'boolean',
            'rating'               => 'float',
            'rating_count'         => 'integer',
            'services'             => 'array',
            'fuel_types'           => 'array',
            'created_at'           => 'datetime',
            'license_issue_date'   => 'date',
            'license_expiry_date'  => 'date',
            'pumps_count'          => 'integer',
        ];
    }

    // Accessor: name ← Flutter يتوقع "name" بدلاً من "station_name"
    public function getNameAttribute(): string
    {
        return $this->station_name ?? '';
    }

    // Accessor: address ← Flutter يتوقع "address" بدلاً من "location"
    public function getAddressAttribute(): string
    {
        return $this->location ?? '';
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
