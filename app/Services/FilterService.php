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
     * @param array  $fields
     * @param string $type
     *
     * @return array
     * @throws FilterServiceGetTypeInvalidException
     */
    private function makeValidationRulesForField(array $fields, string $type): array
    {
        $this->validateType($type);
        $ucType = ucwords(strtolower($type));
        $validMatchModeProperty = "valid{$ucType}MatchModes";

        $valueType = match (strtolower($type)) {
            'boolean' => 'boolean',
            'number' => 'numeric',
            'date' => 'date_format:Y-m-d',
            default => 'string',
        };

        $rules = [];

        foreach ($fields as $field) {
            $rules += [
                "filters.{$field}" => 'nullable|array',
                "filters.{$field}.constraints" => 'nullable|array|max:5',
                "filters.{$field}.constraints.*.mode" => [
                    'nullable',
                    'string',
                    Rule::in($this->$validMatchModeProperty),
                    "required_with:filters.{$field}.constraints.*.value",
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
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidDateMatchModes(bool $asString = false, string $separator = ','): array|string
    {
        return $this->getMatchModes('date', $asString, $separator);
    }

    /**
     * @param bool   $asString
     * @param string $separator
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidStringMatchModes(bool $asString = false, string $separator = ','): array|string
    {
        return $this->getMatchModes('string', $asString, $separator);
    }

    /**
     * @param bool   $asString
     * @param string $separator
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidNumberMatchModes(bool $asString = false, string $separator = ','): array|string
    {
        return $this->getMatchModes('number', $asString, $separator);
    }

    /**
     * @param bool   $asString
     * @param string $separator
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidBooleanMatchModes(bool $asString = false, string $separator = ','): array|string
    {
        return $this->getMatchModes('boolean', $asString, $separator);
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
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getMatchModes(string $type, bool $asString = false, string $separator = ','): array|string
    {
        $this->validateType($type);

        $type = ucwords(strtolower($type));
        $type = "valid{$type}MatchModes";

        if (!$asString) {
            return $this->$type;
        }

        return implode($separator, $this->$type);
    }

    /**
     * @param Builder    $query
     * @param string     $field
     * @param string|int $value
     * @param string     $matchMode
     * @param string     $operator
     *
     * @return void
     * @throws FilterServiceGetTypeInvalidException
     */
    public function queryFilter(Builder $query, string $field, string|int $value, string $matchMode, string $operator): void
    {
        if (in_array(strtolower($matchMode), $this->getMatchModes('date', false, true))) {
            $whereMethod = $operator === 'or' ? 'orWhereDate' : 'whereDate';
            $value = Carbon::parse($value)->format('Y-m-d');
        } else {
            $whereMethod = $operator === 'or' ? 'orWhere' : 'where';
        }

        switch (strtolower($matchMode)) {
            case 'contains':
                $query->$whereMethod($field, 'like', "%$value%");
                break;
            case 'starts_with':
                $query->$whereMethod($field, 'like', "$value%");
                break;
            case 'ends_with':
                $query->$whereMethod($field, 'like', "%$value");
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
            case 'date_gte':
            case 'gte':
                $query->$whereMethod($field, '>=', $value);
                break;
            case 'date_lt':
            case 'lt':
                $query->$whereMethod($field, '<', $value);
                break;
            case 'date_lte':
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
     * @return FilterService
     */
    public function filter(Builder $query, array $filters, array $exceptions = [], array $substitutes = []): FilterService
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
                    foreach ($constraints as $constraint) {
                        if ($constraint['value'] !== null) {
                            $this->queryFilter($query, $field, $constraint['value'], $constraint['mode'], $operator);
                        }
                    }
                });
            }
        }

        return $this;
    }

    /**
     * @param Builder     $query
     * @param string|null $value
     * @param array       $global
     * @param array       $visible
     * @param array       $substitutions
     *
     * @return FilterService
     */
    public function globalFilter(Builder $query, ?string $value, array $global = [], array $visible = [], array $substitutions = []): FilterService
    {
        if ($value === '' || $value === null) {
            return $this;
        }

        foreach ($visible as &$field) {
            $field = $substitutions[$field] ?? $field;
        }

        $columns = array_intersect($visible, $global);

        if (count($columns)) {
            $query->where(function (Builder $query) use ($columns, $value) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', "%{$value}%");
                }
            });
        }

        return $this;
    }

    /**
     * @param Builder $query
     * @param array   $sorts
     * @param array   $exceptions
     * @param array   $substitutes
     *
     * @return FilterService
     */
    public function sort(Builder $query, array $sorts, array $exceptions = [], array $substitutes = []): FilterService
    {
        foreach ($sorts as $sort) {
            if (in_array(strtolower($sort['field']), $exceptions)) {
                continue;
            }

            $sort['field'] = $substitutes[$sort['field']] ?? $sort['field'];

            $query->orderBy($sort['field'], $sort['order'] === 'asc' ? 'asc' : 'desc');
        }

        return $this;
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
