<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use phpDocumentor\Reflection\Types\Boolean;

class Address extends Model
{
    protected $guarded = [];

    public $casts = [
        'default_address' => 'boolean',
    ];

    protected array $validAddressables = [
        Company::class,
        Customer::class,
        User::class,
    ];

    use HasFactory;

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getValidAddressables(): array
    {
        return $this->validAddressables;
    }
}
