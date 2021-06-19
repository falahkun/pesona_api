<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Halaqah extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'halaqah';

    protected $fillable = [
        'name',
        'time_start',
        'time_end',
        'asatidz_id',
    ];
}
