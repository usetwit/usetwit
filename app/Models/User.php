<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements Authorizable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasSlug;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom(['first_name', 'middle_names', 'last_name'])
                          ->saveSlugsTo('slug')->slugsShouldBeNoLongerThan(50);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected static function booted(): void
    {
        parent::booted();

        static::saving(function ($user) {
            $user->full_name = trim(preg_replace('/\s+/', ' ',
                "{$user->first_name} {$user->middle_names} {$user->last_name}"));
        });

        static::deleting(function ($model) {
            $model->active = 0;
            $model->save();
        });

        static::restoring(function ($model) {
            $model->active = 1;
            $model->save();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'joined_at' => 'date:Y-m-d',
            'left_at' => 'date:Y-m-d',
            'active' => 'boolean',
        ];
    }

    /**
     * @return MorphOne
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * @return MorphMany
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * @return MorphMany
     */
    public function profileImages(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->whereType('user_profile');
    }

    public function uploadedImages(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
