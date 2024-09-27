<?php

namespace App\Http\Requests\Users;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Services\FilterService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
    public function rules(FilterService $service): array
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
            ],
            'date' => [
                'join_date',
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
            'join_date',
            'active',
        ]);

        return array_merge($filters, $sort);
    }
}
