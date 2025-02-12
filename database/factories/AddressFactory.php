<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $address = app(Address::class);
        $addressableType = Arr::random($address->validAddressables);

        return [
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->city,
            'address_line_3' => $this->faker->country,
            'postcode' => $this->faker->postcode,
            'country' => $this->faker->countryCode,
            'addressable_type' => $addressableType,
            'addressable_id' => $addressableType::factory()->create()->id,
            'deleted_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
