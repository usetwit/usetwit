<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomOperation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static array $operationTypes = ['process', 'buffer'];

    public static array $bufferDurationTypes = ['minutes', 'calendar_day', 'working_day'];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function Calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }
}
