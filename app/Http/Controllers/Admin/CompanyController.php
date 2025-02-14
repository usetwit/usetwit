<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\UpdateRequest;
use App\Models\Address;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\Intl\Countries;

class CompanyController extends Controller
{
    public function edit(): View
    {
        $countries = Countries::getNames();
        $address = Address::whereType('hq')->first();

        return view('company.company-edit', compact('countries', 'address'));
    }

    public function update(UpdateRequest $request, GeneralSettings $settings): RedirectResponse
    {
        Address::whereType('hq')->first()->update($request->only([
            'address_line_1',
            'address_line_2',
            'address_line_3',
            'postcode',
            'country'
        ]));

        $settings->name = $request->input('name');

        return back()->with('success', 'Company has been updated');
    }
}
