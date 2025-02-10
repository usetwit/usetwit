<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->numerify("{$this->faker->domainWord}###"),
            'first_name' => $this->faker->firstName,
            'middle_names' => $this->faker->optional()->firstName,
            'last_name' => $this->faker->optional()->lastName,
            'full_name' => function (array $attributes) {
                $fullName = "{$attributes['first_name']} {$attributes['middle_names']} {$attributes['last_name']}";

                return preg_replace('/\s+/', ' ', trim($fullName));
            },
            'slug' => function (array $attributes) {
                Str::slug($attributes['full_name']);
            },
            'dob' => $this->faker->boolean(80) ? Carbon::parse('2002-01-01')->addDays(rand(-5000, -1000)) : null,
            'company_number' => $this->faker->optional()->phoneNumber,
            'company_mobile_number' => $this->faker->optional()->phoneNumber,
            'company_ext' => $this->faker->optional()->numerify,
            'personal_number' => $this->faker->phoneNumber,
            'personal_mobile_number' => $this->faker->phoneNumber,
            'employee_id' => $this->faker->optional()->numerify('E#####'),
            'email' => $this->faker->unique()->companyEmail,
            'personal_email' => $this->faker->optional()->safeEmail,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'emergency_name' => $this->faker->optional()->firstName,
            'emergency_number' => $this->faker->phoneNumber,
            'joined_at' => $this->faker->boolean(80) ? Carbon::parse('2024-01-01')->addDays(rand(0, 365)) : null,
            'left_at' => $this->faker->boolean(80) ? Carbon::parse('2024-01-01')->addDays(rand(365, 700)) : null,
            'active' => true,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
