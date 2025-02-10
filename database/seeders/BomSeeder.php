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
        Bom::factory(100)->create();
    }
}
