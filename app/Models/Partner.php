<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $guarded = [];

    public function getLogoUrlAttribute(): string
    {
        if (! $this->logo) {
            return 'https://dummyimage.com/360x180/004B9C/ffffff&text=' . urlencode($this->name);
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        return asset($this->logo);
    }
}
