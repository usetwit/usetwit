<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public $casts = [
        'default_address' => 'boolean',
    ];

    public array $validAddressables = [
        Company::class,
        Customer::class,
        User::class,
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
