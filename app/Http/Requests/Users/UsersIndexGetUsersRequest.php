<?php

namespace App\Http\Requests\Users;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsersIndexGetUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('users.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     * @throws FilterServiceGetTypeInvalidException
     */
    public function rules(FilterService $service, GeneralSettings $settings): array
    {
        $filters = $service->makeValidationFilterRules([
            'string' => [
                'username',
                'email',
                'first_name',
                'middle_names',
                'last_name',
                'full_name',
                'employee_id',
                'role_name',
            ],
            'date' => [
                'joined_at',
            ],
            'boolean' => [
                'active',
            ],
        ]);

        $sort = $service->makeValidationSortRules([
            'username',
            'email',
            'first_name',
            'middle_names',
            'last_name',
            'full_name',
            'employee_id',
            'joined_at',
            'active',
            'role_name',
        ]);

        $perPage = [
            'per_page' => [
                'integer',
                Rule::in($settings->per_page_options),
            ],
        ];

        return array_merge($filters, $sort, $perPage);
    }
}
