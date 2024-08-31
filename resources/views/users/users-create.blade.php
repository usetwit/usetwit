@extends('app.layout')

@section('heading')
    Create New User
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('users.create') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/users-create.js'])
@endsection

@section('content')
    <div id="users-create">
        <users-create route-check-username="{{ $routeCheckUsername }}"
                      route-store="{{ $routeStore }}"
                      route-redirect="{{ $routeRedirect }}"
                      date-format="{{ $dateFormat }}"
                      suggested-id="{{ $suggestedId }}"
                      :roles="{{ Js::from($roles) }}"
                      :countries="{{ Js::from($countries) }}"
                      :selected-country="{{ Js::from($selectedCountry) }}"
        ></users-create>
    </div>
@endsection
