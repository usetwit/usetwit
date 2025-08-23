@extends('admin.layout')

@section('heading')
    Edit Bom Version {{ $bomVersion->version }}
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.bom-versions.edit', $bomVersion) }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/boms-edit.js'])
@endsection

@section('content')
    <div id="app">
        <edit :bomVersion="{{ Js::from($bomVersion) }}"
              :routes="{{ Js::from($routes) }}"
        />
    </div>
@endsection
