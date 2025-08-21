<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsDetailImage extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'news_id',
        'news_image_slider',
    ];

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }
}
