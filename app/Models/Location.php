<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Location extends Model
{
    use HasFactory;

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function calendar(): MorphOne
    {
        return $this->morphOne(Calendar::class, 'calendarable');
    }
}
