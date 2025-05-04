<?php

namespace App\Http\Requests\Company;

use App\Rules\Postcode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'address_line_1' => 'nullable|string|max:100',
            'address_line_2' => 'nullable|string|max:100',
            'address_line_3' => 'nullable|string|max:100',
            'postcode' => new Postcode,
            'country_code' => 'nullable|string|max:2|min:2',
        ];
    }
}
