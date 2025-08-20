<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewsHomePage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'home_id',
        'reviews_heading',
        'reviews_heading_description',
        'testimonial',
        'reviewer_image',
        'reviewer_name',
        'reviewer_designation',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
