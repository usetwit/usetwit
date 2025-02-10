@extends('admin.layout')

@section('heading')
    All Users
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.users.index') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/users-index.js'])
@endsection

@section('content')
    <div id="app" class="mx-0 lg:mx-4">
        <users-index :pagination-settings="{{ Js::from($paginationSettings) }}"
                     :date-settings="{{ Js::from($dateSettings) }}"
                     route-get-users="{{ $routeGetUsers }}"
        ></users-index>
    </div>
@endsection
