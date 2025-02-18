@extends('admin.layout')

@section('heading')
    Edit Location: {{ $location->name }}
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.locations.edit', $location) }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/locations-edit.js'])
@endsection

@section('content')
    <div id="app" class="mx-0 lg:mx-4">
        <locations-edit :date-settings="{{ Js::from($dateSettings) }}"
                        :routes="{{ Js::from($routes) }}"
                        :location="{{ Js::from($location) }}"
                        :countries="{{ Js::from($countries) }}"
                        :permissions="{{ Js::from($permissions) }}"
        ></locations-edit>
    </div>
@endsection
