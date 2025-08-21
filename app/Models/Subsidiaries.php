<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subsidiaries extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subsidiaries_hero_image',
        'subsidiaries_name',
        'subsidiaries_description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    
}
