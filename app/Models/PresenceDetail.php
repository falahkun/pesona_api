<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PresenceDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'presence_id',
        'user_id',
        'presence_type',
        'absent_message',
        'latitude',
        'longitude'
    ];
    
    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
