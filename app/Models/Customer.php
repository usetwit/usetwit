<?php

namespace App\Models;

use App\Exceptions\InvalidCustomerTypeException;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Sluggable\SlugOptions;

class Customer extends Authenticatable implements Authorizable
{
    use HasFactory;

    /**
     * Get the options for generating the slug.
     *
     * @throws InvalidCustomerTypeException
     */
    public function getSlugOptions(): SlugOptions
    {
        $slugOptions = SlugOptions::create()
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50);

        if ($this->isB2C()) {
            return $slugOptions->generateSlugsFrom(['first_name', 'last_name']);
        }

        if ($this->isB2B()) {
            return $slugOptions->generateSlugsFrom('company_name');
        }

        throw new InvalidCustomerTypeException('Invalid customer type: Unable to generate slug options.');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        parent::booted();

        static::saving(function (self $customer) {
            if ($customer->isB2C() && $customer->isDirty(['first_name', 'last_name'])) {
                $customer->full_name = trim("{$customer->first_name} {$customer->last_name}");
            }
        });
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function defaultAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('default_address', true);
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
