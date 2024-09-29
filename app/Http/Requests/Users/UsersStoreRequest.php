<?php

namespace App\Http\Requests\Users;

use App\Settings\GeneralSettings;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsersStoreRequest extends FormRequest
{
    public function __construct()
    {
        @parent::__construct();
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('users.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(GeneralSettings $settings): array
    {
        $countryCodes = array_keys($settings->countries);

        return [
            'username' => 'required|string|unique:users,username|max:255|regex:/^[a-z0-9]+$/',
            'password' => 'required|string|confirmed|max:255',
            'email' => 'nullable|email:strict|max:255',
            'home_email' => 'nullable|email:strict|max:255',
            'first_name' => 'required|string|max:85',
            'middle_names' => 'nullable|string|max:85',
            'last_name' => 'nullable|string|max:85',
            'employee_id' => 'nullable|string|max:255|unique:users,employee_id',
            'joined_at' => 'nullable|date_format:Y-m-d|after_or_equal:2024-01-01|before_or_equal:2050-12-31',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'address_line3' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'company_ext' => 'nullable|string|regex:/^[0-9 ]*$/|max:255',
            'company_number' => 'nullable|string|regex:/^[0-9 \+\(\)\.\-]*$/|max:255',
            'home_number' => 'nullable|string|regex:/^[0-9 \+\(\)\.\-]*$/|max:255',
            'mobile_number' => 'nullable|string|regex:/^[0-9 \+\(\)\.\-]*$/|max:255',
            'emergency_name' => 'nullable|string|max:255',
            'emergency_number' => 'nullable|string|regex:/^[0-9 \+\(\)\.\-]*$/|max:255',
            'role_id' => 'required|integer|exists:roles,id',
            'country' => [
                'nullable',
                Rule::in($countryCodes),
            ],
        ];
    }
}
