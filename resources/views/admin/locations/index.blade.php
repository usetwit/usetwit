@extends('admin.layout')

@section('heading')
    All Locations
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.locations.index') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/locations-index.js'])
@endsection

@section('content')
    <div id="app" class="mx-0 lg:mx-4">
        <locations-index :pagination-settings="{{ Js::from($paginationSettings) }}"
                     :date-settings="{{ Js::from($dateSettings) }}"
                     route-get-locations="{{ $routeGetLocations }}"
        ></locations-index>
    </div>
@endsection
