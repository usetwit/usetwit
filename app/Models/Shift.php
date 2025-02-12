<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Shift extends Model
{
    use HasFactory;

    public function calendar(): MorphOne
    {
        return $this->morphOne(Calendar::class, 'calendarable');
    }
}
