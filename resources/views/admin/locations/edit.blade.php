@extends('admin.layout')

@section('heading')
    All Locations
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.locations.edit') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/locations-edit.js'])
@endsection

@section('content')
    <div id="app" class="mx-0 lg:mx-4">
        <locations-edit :date-settings="{{ Js::from($dateSettings) }}"
                          routes="{{ Js::from($routes) }}"
        ></locations-edit>
    </div>
@endsection
