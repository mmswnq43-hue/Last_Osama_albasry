<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'phone',
        'password_hash',
        'vehicle_type',
        'engine_number',
        'user_role',
        'qr_code',
        'is_active',
        'phone_verified',
        'two_factor_enabled',
        'account_locked',
        'last_location_lat',
        'last_location_lon',
        'last_location_update',
    ];

    protected $hidden = [
        'password_hash',
    ];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'phone_verified' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'account_locked' => 'boolean',
            'last_location_lat' => 'float',
            'last_location_lon' => 'float',
            'last_location_update' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function stations()
    {
        return $this->hasMany(GasStation::class, 'owner_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function refuels()
    {
        return $this->hasMany(Refuel::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function carWashes()
    {
        return $this->hasMany(CarWash::class);
    }

    public function maintenanceServices()
    {
        return $this->hasMany(MaintenanceService::class);
    }

    public function employeeProfile()
    {
        return $this->hasOne(Employee::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function carWashCentersOwned()
    {
        return $this->hasMany(CarWashCenter::class, 'owner_id');
    }

    public function maintenanceCentersOwned()
    {
        return $this->hasMany(MaintenanceCenter::class, 'owner_id');
    }

    public function electronicCards()
    {
        return $this->hasMany(ElectronicCard::class);
    }
}
