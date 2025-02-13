<?php

namespace App\Http\Requests\Locations;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Models\User;
use App\Services\FilterService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetLocationsRequest extends FormRequest
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
     *
     * @throws FilterServiceGetTypeInvalidException
     */
    public function rules(FilterService $service): array
    {
        $filterRules = [
            'string' => [
                'name',
                'address_line_1',
                'address_line_2',
                'address_line_3',
                'country_code',
                'country_name',
                'postcode',
            ],
            'date' => [
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

        return $service->makeValidationRules($filterRules);
    }
}
