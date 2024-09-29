<?php

namespace App\Models;

use App\Models\Scopes\ExcludeSystemScope;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Authorizable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

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
        static::addGlobalScope(new ExcludeSystemScope);

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
            'active' => 'boolean',
        ];
    }

    /**
     * @return MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
