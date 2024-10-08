<?php

namespace App\Http\Requests\Users;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Models\User;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UsersIndexGetUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     * @throws FilterServiceGetTypeInvalidException
     */
    public function rules(FilterService $service, GeneralSettings $settings): array
    {
        $filterRules = [
            'string' => [
                'username',
                'email',
                'first_name',
                'middle_names',
                'last_name',
                'full_name',
                'employee_id',
                'role_name',
                'global',
            ],
            'date' => [
                'joined_at',
                'created_at',
                'updated_at',
            ],
            'boolean' => [
                'active',
            ],
            'number' => [
                'id',
            ],
        ];

        $filters = $service->makeValidationFilterRules($filterRules);

        $sort = $service->makeValidationSortRules(array_diff(Arr::flatten($filterRules), ['global']));

        $perPage = [
            'per_page' => [
                'integer',
                Rule::in($settings->per_page_options),
            ],
        ];

        $visible = [
            'visible' => 'array',
            'visible.*' => [
                'string',
                Rule::in(array_diff(Arr::flatten($filterRules), ['global'])),
            ],
        ];

        return array_merge($filters, $sort, $perPage, $visible);
    }
}
