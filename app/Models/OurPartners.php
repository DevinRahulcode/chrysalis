<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OurPartners extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'home_id',
        'partner_image',
        'partner_title',
        'partner_description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
