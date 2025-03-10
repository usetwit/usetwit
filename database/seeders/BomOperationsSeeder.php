<?php

namespace Database\Seeders;

use App\Models\BomOperation;
use Illuminate\Database\Seeder;

class BomOperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 11; $i++) {

            BomOperation::factory()->create([
                'bom_id' => 1,
                'operation_id' => $i,
                'calendar_id' => 2,
                'type' => 'process',
                'buffer_duration_type' => null,
                'buffer_duration' => null,
                'x' => 5,
                'y' => $i * 40,
            ]);
        }

        BomOperation::factory()->create([
            'bom_id' => 1,
            'operation_id' => null,
            'calendar_id' => 2,
            'type' => 'buffer',
            'buffer_duration_type' => 'calendar_day',
            'buffer_duration' => 2,
            'x' => 40,
            'y' => 0,
        ]);
    }
}
