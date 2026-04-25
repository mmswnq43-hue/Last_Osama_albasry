<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'security_logs';

    protected $fillable = [
        'user_id',
        'log_type',
        'session_id',
        'qr_code',
        'ip_address',
        'user_lat',
        'user_lon',
        'service_lat',
        'service_lon',
        'distance_meters',
        'is_successful',
        'error_message',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'user_lat' => 'float',
            'user_lon' => 'float',
            'service_lat' => 'float',
            'service_lon' => 'float',
            'distance_meters' => 'float',
            'is_successful' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
