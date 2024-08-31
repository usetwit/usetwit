@extends('app.layout')

@section('heading')
    All Users
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('users.index') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/users-index.js'])
@endsection

@section('content')
    <div id="users-index">
        <users-index :default-per-page="{{ $defaultPerPage }}"
                     :per-page-options="{{ Js::from($perPageOptions) }}"
                     route-get-users="{{ $routeGetUsers }}"
        ></users-index>
    </div>
@endsection
