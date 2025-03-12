<?php

namespace Database\Factories;

use App\Models\Bom;
use Illuminate\Database\Eloquent\Factories\Factory;

class BomVersionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bom_id' => Bom::factory(),
            'bom_version' => $this->faker->randomDigit(),
            'comments' => $this->faker->text(),
        ];
    }
}
