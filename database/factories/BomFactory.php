<?php

namespace Database\Factories;

use App\Models\Bom;
use Illuminate\Database\Eloquent\Factories\Factory;

class BomFactory extends Factory
{
    protected $model = Bom::class;

    public function definition(): array
    {
        return [
            'long_id' => strtoupper($this->faker->unique()->word),
            'description' => $this->faker->sentence,
        ];
    }
}
