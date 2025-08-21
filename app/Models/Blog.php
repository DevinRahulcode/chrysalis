<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model


{

    use SoftDeletes;

    protected $fillable = [
        'title',
        'order',
        'card_image',
        'description',
        'other_blogs_description',
        'listing_description',
        'slug',
        'related_post_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
