<?php

namespace Database\Seeders;

use App\Models\Bom;
use App\Models\BomOperation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BomOperationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=1;$i<=11;$i++){

            BomOperation::factory()->create([
                'bom_id' => 1,
                'operation_id' => $i,
                'calendar_id' => 2,
                'x' => 5,
                'y' => $i*40,
            ]);
        }
    }
}
