<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $type = Arr::random(['b2b', 'b2c']);

        return [
            'type' => $type,
            'first_name' => $type === 'b2c' ? $this->faker->name() : null,
            'last_name' => $type === 'b2c' ? $this->faker->name() : null,
            'company_name' => $type === 'b2b' ? $this->faker->company() : null,
            'slug' => fn (array $attributes) => Str::slug($type === 'b2c' ? "{$attributes['first_name']} {$attributes['last_name']}" : $attributes['company_name']),
            'comments' => $this->faker->optional()->text(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
