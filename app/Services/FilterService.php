<?php

namespace App\Services;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Rules\HasMultipleConstraints;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Database\Query\Builder;

class FilterService
{
    protected array $validStringIntMatchModes = [
        'contains',
        'startsWith',
        'endsWith',
        'equals',
        'notEquals',
        'gt',
        'gte',
        'lt',
        'lte',
    ];

    protected array $validDateMatchModes = [
        'dateIs',
        'dateIsNot',
        'dateBefore',
        'dateAfter',
    ];

    protected array $validBooleanMatchModes = [
        'equals',
    ];

    protected array $validTypes = ['StringInt', 'Date', 'Boolean'];

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

    private function makeValidationRulesForField(array $fields, string $type): array
    {
        $rules = [];
        $validMatchModeProperty = "valid{$type}MatchModes";
        $valueType = $type === 'Boolean' ? 'boolean' : 'string';

        foreach ($fields as $field) {
            $rules += [
                "filters.{$field}" => 'nullable|array',
                "filters.{$field}.constraints" => 'nullable|array',
                "filters.{$field}.constraints.*.matchMode" => [
                    'nullable',
                    'string',
                    Rule::in($this->$validMatchModeProperty),
                    "present_with:filters.{$field}.constraints.*.value",
                ],
                "filters.{$field}.constraints.*.value" => [
                    'nullable',
                    "present_with:filters.{$field}.constraints.*.matchMode",
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
        return $this->getArrayOrStringMatchModes('Date', $asString, $lowerCase, $separator);
    }

    /**
     * @param bool   $asString
     * @param string $separator
     * @param bool   $lowerCase
     *
     * @return array|string
     * @throws FilterServiceGetTypeInvalidException
     */
    public function getValidStringIntMatchModes(bool $asString = false, bool $lowerCase = false, string $separator = ','): array|string
    {
        return $this->getArrayOrStringMatchModes('StringInt', $asString, $lowerCase, $separator);
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
        return $this->getArrayOrStringMatchModes('Boolean', $asString, $lowerCase, $separator);
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
    private function getArrayOrStringMatchModes(string $type, bool $asString = false, bool $lowerCase = false, string $separator = ','): array|string
    {
        $this->validateType($type);

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
        if (in_array(strtolower($matchMode), $this->getArrayOrStringMatchModes('Date', false, true))) {
            $whereMethod = $operator === 'or' && $index > 0 ? 'orWhereDate' : 'whereDate';
            $value = Carbon::parse($value)->format('Y-m-d');
        } else {
            $whereMethod = $operator === 'or' && $index > 0 ? 'orWhere' : 'where';
        }

        switch (strtolower($matchMode)) {
            case 'contains':
                $query->$whereMethod($field, 'LIKE', "%$value%");
                break;
            case 'startswith':
                $query->$whereMethod($field, 'LIKE', "$value%");
                break;
            case 'endswith':
                $query->$whereMethod($field, 'LIKE', "%$value");
                break;
            case 'dateis':
            case 'equals':
//                dd($whereMethod);
                $query->$whereMethod($field, '=', $value);
                break;
            case 'dateisnot':
            case 'notequals':
                $query->$whereMethod($field, '!=', $value);
                break;
            case 'dateafter':
            case 'gt':
                $query->$whereMethod($field, '>', $value);
                break;
            case 'gte':
                $query->$whereMethod($field, '>=', $value);
                break;
            case 'datebefore':
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
                            $this->queryFilter($query, $field, $props['value'], $index, $props['matchMode'], $operator);
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

            $query->orderBy($sort['field'], $sort['order'] === 1 ? 'asc' : 'desc');
        }
    }
}
