<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubsidiariesCard extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'subsidiaries_id',
        'card_image',
        'card_heading',
        'card_description',
        'card_url',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
