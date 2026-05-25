<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'objectives' => 'array',
        'opportunities' => 'array',
        'program' => 'array',
        'skills' => 'array',
        'is_featured' => 'boolean',
    ];
}
