@extends('app.content')

@section('heading')
    Edit Company Information
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('company.edit') }}
@endsection

@section('app.content')

    <form method="post">
        @method('patch')
        @csrf

        <x-forms.wrapper title="Company Name" name="name" required>
            <x-forms.text-input name="name"
                                maxlength="50"
                                placeholder="Company Name..."
                                required
                                :value="$company->name"
            />
        </x-forms.wrapper>

        <x-forms.wrapper title="Address" name="address_line_1">
            <x-forms.text-input name="address_line_1"
                                maxlength="50"
                                class="mb-4"
                                placeholder="Address..."
                                :value="$address->address_line_1"
            />

            <x-forms.text-input name="address_line_2"
                                maxlength="50"
                                class="mb-4"
                                placeholder="City..."
                                :value="$address->address_line_2"
            />

            <x-forms.text-input name="address_line_3"
                                maxlength="50"
                                class="mb-4"
                                placeholder="Region/County..."
                                :value="$address->address_line_3"
            />

            <x-forms.text-input
                name="postcode"
                maxlength="10"
                class="mb-4"
                placeholder="Postcode..."
                :value="$address->postcode"
            />

            <x-forms.select
                :options="$countries"
                name="country"
                placeholder="Country..."
                :value="$address->country"
            />
        </x-forms.wrapper>

        <x-forms.button text="Save" icon="save"/>
    </form>

@endsection
