<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;
    protected $table = 'user_activity_logs';
    protected $fillable = ['user_id','action_type','title','description','meta','icon','color','created_at'];
    protected function casts(): array {
        return ['meta' => 'array', 'created_at' => 'datetime'];
    }
    public function user() { return $this->belongsTo(User::class); }
}
