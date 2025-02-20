<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'comments' => $this->faker->text(),
            'type' => Arr::random(['b2b', 'b2c']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
