<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

     protected $fillable = [
        'news_title',
        'order',
        'date',
        'thumbnail',
        'news_description',
        'status',
        'slug',
        'page_title',
        'description',
        'keywords',
        'og_image',
        'og_title',
        'og_description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


}
