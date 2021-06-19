<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presence extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;
    
    protected $with = [
        'schedule'    
    ];
    
    protected $fillable = [
        'id',
        'date',
        'schedule_id',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    public function schedule() {
        return $this->hasOne(Schedule::class, 'id', 'schedule_id');
    }
    
    public function users() {
        return $this->hasMany(PresenceDetail::class, 'presence_id', 'id');
    }
    
}
