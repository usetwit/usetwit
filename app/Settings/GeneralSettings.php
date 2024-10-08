<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public array $countries;
    public string $locale;
    public array $locales;
    public string $currency;
    public array $currencies;
    public int $employee_id_padding;
    public string $employee_id_prefix;
    public string $default_country;
    public int $per_page_default;
    public array $per_page_options;
    public array $date_validation_separators;
    public array $date_validation_regex;
    public string $date_validation_separator_default;
    public string $date_validation_default;

    /**
     * @return string
     */
    public static function group(): string
    {
        return 'general';
    }

    /**
     * Returns either the default country or country specified
     *
     * @param string|null $code
     *
     * @return array
     */
    public function countryAsObjectForJson(?string $code = null): array
    {
        if ($code === null) {
            return ['code' => $this->default_country, 'name' => $this->countries[$this->default_country]];
        }

        return ['code' => $code, 'name' => $this->countries[$code]];
    }

    /**
     * Replace hyphens with default separator
     *
     * @return string
     */
    public function dateFormatForDisplay(): string
    {
        $format = str_replace('-', $this->date_validation_separator_default, $this->date_validation_default);

        return strtolower($format);
    }

    /**
     * @return array
     */
    public function dateFormats(): array
    {
        return array_keys(array_change_key_case($this->date_validation_regex));
    }

    public function dateRegexDefault(): string
    {
        return $this->date_validation_regex[$this->date_validation_default];
    }

    public function dateSettings(): array
    {
        return [
            'regex' => $this->dateRegexDefault(),
            'display' => $this->dateFormatForDisplay(),
            'separator' => $this->date_validation_separator_default,
            'format' => $this->date_validation_default,
        ];
    }

    public function paginationSettings(): array
    {
        return [
            'per_page' => [
                'default' => $this->per_page_default,
                'options' => $this->per_page_options,
            ],
        ];
    }
}
