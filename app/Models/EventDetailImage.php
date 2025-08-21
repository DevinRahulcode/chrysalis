<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDetailImage extends Model
{
    protected $fillable = [
        'event_id',
        'event_image_slider',
        'order',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
