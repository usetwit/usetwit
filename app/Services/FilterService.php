<?php

namespace App\Services;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Rules\HasMultipleConstraints;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Database\Query\Builder;

class FilterService
{
    protected array $validStringMatchModes = [
        'contains',
        'starts_with',
        'ends_with',
        'equals',
        'not_equals',
        'gt',
        'gte',
        'lt',
        'lte',
    ];

    protected array $validNumberMatchModes = [
        'contains',
        'starts_with',
        'ends_with',
        'equals',
        'not_equals',
        'gt',
        'gte',
        'lt',
        'lte',
    ];

    protected array $validDateMatchModes = [
        'date_equals',
        'date_not_equals',
        'date_before',
        'date_after',
        'date_gt',
        'date_gte',
        'date_lt',
        'date_lte',
    ];

    protected array $validBooleanMatchModes = [
        'equals',
    ];

    protected array $validTypes = ['string', 'number', 'date', 'boolean'];

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    public function makeValidationFilterRules(string|array $constraintType, array $fields = []): array
    {
        if (is_string($constraintType)) {
            $validationArray = [
                $constraintType => $fields,
            ];
        }

        $fieldsRules = [];

        foreach ($validationArray ?? $constraintType as $type => $fields) {
            $this->validateType($type);

            if (is_string($fields)) {
                $fields = [$fields];
            }

            $fieldsRules += $this->makeValidationRulesForField($fields, $type);
        }

        return $fieldsRules;
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    private function makeValidationRulesForField(array $fields, string $type): array
    {
        $this->validateType($type);
        $ucType = ucwords(strtolower($type));
        $validMatchModeProperty = "valid{$ucType}MatchModes";

        $valueType = match (strtolower($type)) {
            'boolean' => 'boolean',
            'number' => 'number',
            'date' => 'date_format:Y-m-d',
            default => 'string',
        };

        $rules = [];

        foreach ($fields as $field) {
            $rules += [
                "filters.{$field}" => 'nullable|array',
                "filters.{$field}.constraints" => 'nullable|array',
                "filters.{$field}.constraints.*.mode" => [
                    'nullable',
                    'string',
                    Rule::in($this->$validMatchModeProperty),
                    "present_with:filters.{$field}.constraints.*.value",
                ],
                "filters.{$field}.constraints.*.value" => [
                    'nullable',
                    "present_with:filters.{$field}.constraints.*.mode",
                    $valueType,
                ],
                "filters.{$field}.operator" => [
                    'nullable',
                    'in:and,or',
                    new HasMultipleConstraints(request()->input("filters.{$field}.constraints")),
                ],
            ];
        }

        return $rules;
    }


    /**
     * @param bool   $asString
     * @param string $separator
     * @param bool   $lowerCase
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidDateMatchModes(bool $asString = false, bool $lowerCase = false, string $separator = ','): array|string
    {
        return $this->getMatchModes('date', $asString, $lowerCase, $separator);
    }

    /**
     * @param bool   $asString
     * @param string $separator
     * @param bool   $lowerCase
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidStringMatchModes(bool $asString = false, bool $lowerCase = false, string $separator = ','): array|string
    {
        return $this->getMatchModes('string', $asString, $lowerCase, $separator);
    }

    /**
     * @param bool   $asString
     * @param string $separator
     * @param bool   $lowerCase
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidNumberMatchModes(bool $asString = false, bool $lowerCase = false, string $separator = ','): array|string
    {
        return $this->getMatchModes('number', $asString, $lowerCase, $separator);
    }

    /**
     * @param bool   $asString
     * @param string $separator
     * @param bool   $lowerCase
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidBooleanMatchModes(bool $asString = false, bool $lowerCase = false, string $separator = ','): array|string
    {
        return $this->getMatchModes('boolean', $asString, $lowerCase, $separator);
    }

    /**
     * @throws FilterServiceGetTypeInvalidException
     */
    private function validateType(string $type): void
    {
        if (!in_array($type, $this->validTypes)) {
            throw new FilterServiceGetTypeInvalidException("Invalid type '$type' provided");
        }
    }

    /**
     * @param string $type
     * @param bool   $asString
     * @param string $separator
     * @param bool   $lowerCase
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getMatchModes(string $type, bool $asString = false, bool $lowerCase = false, string $separator = ','): array|string
    {
        $this->validateType($type);

        $type = ucwords(strtolower($type));
        $type = "valid{$type}MatchModes";

        if (!$asString) {
            return $lowerCase ? array_map('strtolower', $this->$type) : $this->$type;
        }

        return implode($separator, $this->$type);
    }

    /**
     * @param Builder    $query
     * @param string     $field
     * @param string|int $value
     * @param int        $index
     * @param string     $matchMode
     * @param string     $operator
     *
     * @return void
     * @throws FilterServiceGetTypeInvalidException
     */
    public function queryFilter(Builder $query, string $field, string|int $value, int $index, string $matchMode, string $operator): void
    {
        if (in_array(strtolower($matchMode), $this->getMatchModes('date', false, true))) {
            $whereMethod = $operator === 'or' && $index > 0 ? 'orWhereDate' : 'whereDate';
            $value = Carbon::parse($value)->format('Y-m-d');
        } else {
            $whereMethod = $operator === 'or' && $index > 0 ? 'orWhere' : 'where';
        }

        switch (strtolower($matchMode)) {
            case 'contains':
                $query->$whereMethod($field, 'LIKE', "%$value%");
                break;
            case 'starts_with':
                $query->$whereMethod($field, 'LIKE', "$value%");
                break;
            case 'ends_with':
                $query->$whereMethod($field, 'LIKE', "%$value");
                break;
            case 'date_equals':
            case 'equals':
                $query->$whereMethod($field, '=', $value);
                break;
            case 'date_not_equals':
            case 'not_equals':
                $query->$whereMethod($field, '!=', $value);
                break;
            case 'date_gt':
            case 'gt':
                $query->$whereMethod($field, '>', $value);
                break;
            case 'gte':
                $query->$whereMethod($field, '>=', $value);
                break;
            case 'date_lt':
            case 'lt':
                $query->$whereMethod($field, '<', $value);
                break;
            case 'lte':
                $query->$whereMethod($field, '<=', $value);
                break;
        }
    }

    /**
     * @param Builder $query
     * @param array   $filters
     * @param array   $exceptions
     * @param array   $substitutes
     *
     * @return void
     */
    public function filter(Builder $query, array $filters, array $exceptions = [], array $substitutes = []): void
    {
        foreach ($filters as $field => $props) {
            if (in_array($field, $exceptions)) {
                continue;
            }

            $field = $substitutes[$field] ?? $field;

            $operator = $props['operator'] ?? 'and';
            $constraints = $props['constraints'] ?? null;

            if ($constraints !== null) {
                $query->where(function (Builder $query) use ($field, $operator, $constraints) {
                    foreach ($constraints as $index => $props) {
                        if ($props['value'] !== null) {
                            $this->queryFilter($query, $field, $props['value'], $index, $props['mode'], $operator);
                        }
                    }
                });
            }
        }
    }

    /**
     * @param Builder $query
     * @param array   $sorts
     * @param array   $exceptions
     * @param array   $substitutes
     *
     * @return void
     */
    public function sort(Builder $query, array $sorts, array $exceptions = [], array $substitutes = []): void
    {
        foreach ($sorts as $sort) {
            if (in_array(strtolower($sort['field']), $exceptions)) {
                continue;
            }

            $sort['field'] = $substitutes[$sort['field']] ?? $sort['field'];

            $query->orderBy($sort['field'], $sort['order'] === 'asc' ? 'asc' : 'desc');
        }
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    public function makeValidationSortRules(array $fields): array
    {
        return [
            'sort' => 'nullable|array',
            'sort.*.field' => [
                'required_with:sort.*.order',
                Rule::in($fields),
            ],
            'sort.*.order' => 'required_with:sort.*.field|in:asc,desc',
        ];
    }
}
