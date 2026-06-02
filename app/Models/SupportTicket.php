<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'support_tickets';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'admin_response',
        'resolved_at',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
