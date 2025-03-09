<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomOperation extends Model
{
    use HasFactory;

    public $guarded = [];

    public static array $operationTypes = ['process', 'buffer'];

    public static array $bufferDurationTypes = ['minutes', 'calendar_day', 'working_day'];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }
}
