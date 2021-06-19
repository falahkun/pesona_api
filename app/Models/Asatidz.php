<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asatidz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'learning_materials',
    ];
}
