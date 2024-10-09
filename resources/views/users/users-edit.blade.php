@extends('app.layout')

@section('heading')
    Edit User
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('users.edit') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/users-edit.js'])
@endsection

@section('content')
    <div id="app">
        <users-edit :routes="{{ Js::from($routes) }}"
                    :roles="{{ Js::from($roles) }}"
                    :permissions="{{ Js::from($permissions) }}"
                    :user="{{ Js::from($user) }}"
                    :countries="{{ Js::from($countries) }}"
        ></users-edit>
    </div>
@endsection
