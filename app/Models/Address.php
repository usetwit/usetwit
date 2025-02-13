<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Intl\Countries;

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

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('country_code')) {
                $model->country_name = Countries::getName($model->country_code, config('app.locale'));
            }
        });

        static::deleting(function ($model) {
            $model->update(['active' => 0]);
        });

        static::restoring(function ($model) {
            $model->update(['active' => 1]);
        });
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
