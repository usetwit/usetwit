<?php

namespace App\Http\Requests\Admin\Addresses;

use App\Rules\Postcode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\Intl\Countries;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('editUserAddress', $this->route('address'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'address_line_3' => 'nullable|string|max:255',
//            'postcode' => 'nullable|string|max:1|regex:/^[A-Za-z0-9\-\s]+$/',
            'postcode' => new Postcode,
            'country_code' => [
                'nullable',
                Rule::in(Countries::getCountryCodes()),
            ],
        ];
    }
}
