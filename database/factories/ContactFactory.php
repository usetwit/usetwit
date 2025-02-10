<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        $contactableTypes = Contact::getValidContactables();
        $contactableType = Arr::random($contactableTypes);

        return [
            'first_name' => $this->faker->optional()->name,
            'last_name' => $this->faker->optional()->lastName,
            'role' => $this->faker->optional()->jobTitle,
            'email' => $this->faker->optional()->email,
            'company_number' => $this->faker->optional()->phoneNumber,
            'company_ext' => $this->faker->numberBetween(100, 999),
            'comments' => $this->faker->sentence,
            'user_id' => User::factory()->create()->id,
            'contactable_type' => $contactableType,
            'contactable_id' => $contactableType::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
