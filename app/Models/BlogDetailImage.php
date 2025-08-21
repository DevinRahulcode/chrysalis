<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogDetailImage extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'blog_id',
        'blog_image_slider',
        'order',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}
