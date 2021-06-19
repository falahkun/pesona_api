<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $with = [
        'halaqah'    
    ];

    protected $fillable = [
        'day_id',
        'halaqah_id',
        'time_start',
        'time_end',
        'session',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
    
    public function halaqah() {
        return $this->hasOne(Halaqah::class, 'id', 'halaqah_id');
    }
}
