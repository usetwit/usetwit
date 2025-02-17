@extends('admin.layout')

@section('heading')
    All Locations
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.locations.create') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/locations-create.js'])
@endsection

@section('content')
    <div id="app" class="mx-0 lg:mx-4">
        <locations-create :date-settings="{{ Js::from($dateSettings) }}"
                          route-create="{{ $routeCreate }}"
        ></locations-create>
    </div>
@endsection
