<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\PotentiallyTranslatedString;

class StockOrBom implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $existsInStockItems = DB::table('stock_items')->where('long_id', $value)->exists();
        $existsInBoms = DB::table('boms')->where('long_id', $value)->exists();

        if (!$existsInStockItems && !$existsInBoms) {
            $fail("The {$attribute} must exist in either the stock_items or boms table.");
        }
    }
}
