<?php

namespace App\Http\Requests\SalesOrders;

use App\Rules\StockOrBom;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class SalesOrdersStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.long_id' => ['required', 'string', 'max:255', new StockOrBom()],
            'items.*.price' => 'required|decimal:0,3|min:0|max:1000000000',
            'items.*.discount' => 'required|decimal:0,2|min:0|max:100',
            'items.*.batches' => 'required|array',
            'items.*.batches.*' => 'required|decimal:0,3|min:0.001|max:100000000000',
            'items.*.due_date' => 'required|date_format:Y-m-d|after_or_equal:2020-01-01|before_or_equal:2050-31-12',
        ];
    }
}
