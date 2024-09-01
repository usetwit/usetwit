<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public array $countries;
    public string $date_format_input;
    public string $date_format_php;
    public string $locale;
    public array $locales;
    public string $currency;
    public array $currencies;
    public int $employee_id_padding;
    public string $employee_id_prefix;
    public string $default_country;
    public int $users_index_per_page;
    public array $users_index_per_page_options;

    /**
     * @return string
     */
    public static function group(): string
    {
        return 'general';
    }

    /**
     * @return array
     */
    public function countriesAsArrayForJson(): array
    {
        $array = [];

        foreach ($this->countries as $code => $country) {
            $array[] = ['code' => $code, 'name' => $country];
        }

        return $array;
    }

    /**
     * Returns either the default country or country specified
     *
     * @param string|null $code
     * @return array
     */
    public function countryAsObjectForJson(?string $code = null): array
    {
       if ($code === null) {
           return ['code' => $this->default_country, 'name' => $this->countries[$this->default_country]];
       }

       return ['code' => $code, 'name' => $this->countries[$code]];
    }
}
