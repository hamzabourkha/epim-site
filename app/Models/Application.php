<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $guarded = [];

    protected $casts = [
        'documents' => 'array',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}
