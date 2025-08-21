<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_title',
        'event_order',
        'event_card_image',
        'event_description',
        'event_other_description',
        'event_listing_description',
        'slug',
        'event_related_post_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
