<?php

namespace Database\Seeders;

use App\Models\Bom;
use App\Models\StockItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class StockItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $existingBomLongIds = Bom::pluck('long_id')->toArray();
        $uniqueLongIds = [];

        while (count($uniqueLongIds) < 200) {
            $wordBasedId = Str::random(8);

            if (!in_array($wordBasedId, $existingBomLongIds) && !in_array($wordBasedId, $uniqueLongIds)) {
                $uniqueLongIds[] = $wordBasedId;
            }
        }

        foreach ($uniqueLongIds as $longId) {
            StockItem::factory()->create([
                'long_id' => strtoupper($longId),
            ]);
        }
    }
}
