<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceService extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'maintenance_services';

    protected $fillable = [
        'user_id',
        'center_id',
        'subscription_id',
        'employee_id',
        'service_type',
        'description',
        'cost',
        'qr_code_used',
        'service_date',
        'completed_at',
        'next_service_date',
        'notes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'service_date' => 'datetime',
            'completed_at' => 'datetime',
            'next_service_date' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function center()
    {
        return $this->belongsTo(MaintenanceCenter::class, 'center_id');
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
