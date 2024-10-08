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
                    :role-id="{{ Js::from($roleId) }}"
                    :roles="{{ Js::from($roles) }}"
                    :permissions="{{ Js::from($permissions) }}"
                    :images="{{ Js::from($images) }}"
                    :user="{{ Js::from($user) }}"
                    :address="{{ Js::from($address) }}"
        ></users-edit>
    </div>
@endsection
