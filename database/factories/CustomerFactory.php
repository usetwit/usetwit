<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'code' => strtoupper(substr($this->faker->name(), 0, 20)),
            'comments' => $this->faker->text(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
