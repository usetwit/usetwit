@extends('admin.layout')

@section('heading')
    Edit Bom
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.bom.edit', $bom) }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/bom-edit.js'])
@endsection

@section('content')
    <div id="app">
        <bom-edit :bom="{{ Js::from($bom) }}"
                  :routes="{{ Js::from($routes) }}"></bom-edit>
    </div>
@endsection
