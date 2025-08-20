<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HomeMainSlider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'home_id',
        'slider_image',
        'slider_heading',
        'slider_description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
