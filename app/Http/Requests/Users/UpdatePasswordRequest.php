<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use App\Rules\PasswordStrength;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $modifiedUser = $this->route('user');

        $authUser = $this->user();

        return $authUser->can('updatePassword', $modifiedUser) || $authUser->can('overridePassword', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'new_password' => [
                'required',
                'confirmed',
                new PasswordStrength(),
            ],
        ];

        if (!$this->user()->can('overridePassword', User::class)) {
            $rules += [
                'current_password' => [
                    'required',
                    'current_password',
                ],
            ];
        }

        return $rules;
    }
}
