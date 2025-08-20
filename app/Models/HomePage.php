<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomePage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'linkedin_link',
        'youtube_link',
        'facebook_link',
        'instagram_link',
        'x_link',
        'about_section_heading',
        'about_section_description',
        'our_business_heading',
        'our_business_description',
        'icon_one',
        'text_one',
        'icon_two',
        'text_two',
        'icon_three',
        'text_three',
        'icon_four',
        'text_four',
        'icon_five',
        'text_five',
        'icon_six',
        'text_six',
        'icon_seven',
        'text_seven',
        'image_icon',
        'your_contribution_heading',
        'your_contribution_description',
        'your_contribution_image',
    ];
}
