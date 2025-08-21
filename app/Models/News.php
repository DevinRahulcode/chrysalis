<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'news_title',
        'news_order',
        'news_card_image',
        'news_description',
        'news_other_description',
        'news_listing_description',
        'slug',
        'news_related_post_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


}
