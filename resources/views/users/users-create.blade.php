@extends('app.layout')

@section('heading')
    Create New User
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('users.create') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/users-create.js'])
@endsection

@section('content')
    <div id="app">
        <users-create route-check-username="{{ $routeCheckUsername }}"
                      route-store="{{ $routeStore }}"
                      route-redirect="{{ $routeRedirect }}"
                      :date-settings="{{ Js::from($dateSettings) }}"
                      suggested-id="{{ $suggestedId }}"
                      :roles="{{ Js::from($roles) }}"
                      :countries="{{ Js::from($countries) }}"
                      :selected-country="{{ Js::from($selectedCountry) }}"
        ></users-create>
    </div>
@endsection
