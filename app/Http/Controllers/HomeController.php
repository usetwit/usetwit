<?php

namespace App\Http\Controllers;

use App\Settings\GeneralSettings;

class HomeController extends Controller
{
    public function index(GeneralSettings $settings)
    {
        $displayFormat = $settings->dateFormatForDisplay();
        $format = $settings->date_validation_default;
        $regex = $settings->dateRegexDefault();
        $separator = $settings->date_validation_separator_default;

        return view('home', compact('displayFormat', 'format', 'regex', 'separator'));
    }
}
