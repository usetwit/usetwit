<?php

namespace Database\Seeders;

use App\Models\Bom;
use Illuminate\Database\Seeder;

class BomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bom::factory()->create(['name' => 'B5975']);
        Bom::factory(25)->create();
    }
}
