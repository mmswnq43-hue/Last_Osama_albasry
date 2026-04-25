<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectronicCard extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'electronic_cards';

    protected $fillable = [
        'user_id',
        'card_number',
        'generated_at',
        'monthly_liters_at_generation',
        'is_used',
        'used_at',
        'priority_station_id',
        'expires_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
            'monthly_liters_at_generation' => 'decimal:2',
            'is_used' => 'boolean',
            'used_at' => 'datetime',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priorityStation()
    {
        return $this->belongsTo(GasStation::class, 'priority_station_id');
    }
}
