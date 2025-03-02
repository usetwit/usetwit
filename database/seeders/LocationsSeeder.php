<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $location1 = Location::factory()->create(['name' => 'London']);
        $location1->calendar()->create();

        $location2 = Location::factory()->create(['name' => 'Birmingham']);
        $location2->calendar()->create();

        $location3 = Location::factory()->create(['name' => 'Manchester']);
        $location3->calendar()->create();
    }
}
