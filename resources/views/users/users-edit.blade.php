@extends('admin.layout')

@section('heading')
    Edit User: {{ $user->full_name }}
    @if($user->active)
        <span class="inline-flex items-center text-green-500 text-sm ml-2"><i class="pi pi-check-circle mr-1"></i> Active</span>
    @else
        <span class="inline-flex items-center text-red-500 text-sm ml-2"><i class="pi pi-times-circle mr-1"></i> Inactive</span>
    @endif
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.users.edit', $user) }}
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
                    :date-settings="{{ Js::from($dateSettings) }}"
        ></users-edit>
    </div>
@endsection
