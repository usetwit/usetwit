<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\CompanyUpdateRequest;
use App\Models\Address;
use App\Settings\CompanySettings;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function edit(CompanySettings $company, GeneralSettings $generalSettings): View
    {
        $countries = $generalSettings->countries;
        $address = Address::whereType('hq')->first();

        return view('company.company-edit', compact('countries', 'address', 'company'));
    }

    public function update(CompanyUpdateRequest $request, CompanySettings $companySettings)
    {
        Address::whereType('hq')->first()->update($request->only([
            'address_line_1',
            'address_line_2',
            'address_line_3',
            'postcode',
            'country'
        ]));

        $companySettings->name = $request->input('name');

        return back()->with('success', 'Company has been updated');
    }
}
