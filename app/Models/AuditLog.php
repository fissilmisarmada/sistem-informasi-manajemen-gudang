<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id','model_type','model_id','action','old_values','new_values','url','ip','user_agent'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'datetime'];

    public function user(){ return $this->belongsTo(User::class); }
}
