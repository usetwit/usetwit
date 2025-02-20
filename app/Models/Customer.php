<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements Authorizable
{
    use HasFactory;

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function contacts(): ?MorphMany
    {
        return $this->isB2B() ? $this->morphMany(Contact::class, 'contactable') : null;
    }

    public function isB2B(): bool
    {
        return $this->type === 'B2B';
    }

    public function isB2C(): bool
    {
        return $this->type === 'B2C';
    }
}
