<?php

namespace App\Http\Requests\BomOperationsNetwork;

use App\Models\Bom;
use App\Rules\AcyclicGraph;
use App\Rules\ArrayUniqueValues;
use App\Rules\MultipleOfTen;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit', Bom::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'operations' => [
                'array',
                'required',
                new AcyclicGraph(),
            ],
            'operations.*.id' => 'required|exists:bom_operations,id',
            'operations.*.x' => [
                'required_with:operations.*.id',
                'integer',
                'min:0',
                'max:5000',
                new MultipleOfTen(),
            ],
            'operations.*.y' => [
                'required_with:operations.*.id',
                'integer',
                'min:0',
                'max:5000',
                new MultipleOfTen(),
            ],
            'operations.*.color' => [
                Rule::in([
                    'blue',
                    'green',
                    'orange',
                    'pink',
                    'purple',
                    'red',
                    'teal',
                    'yellow',
                ]),
            ],
            'operations.*.successors' => [
                'array',
                new ArrayUniqueValues(),
            ],
            'operations.*.successors.*' => 'exists:bom_operations,id',
        ];
    }
}
