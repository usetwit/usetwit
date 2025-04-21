@extends('admin.layout')

@section('heading')
    Edit Bom
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.boms.edit', $bom) }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/boms-edit.js'])
@endsection

@section('content')
    <div id="app">
        <bom-edit :bom="{{ Js::from($bom) }}"
                  :routes="{{ Js::from($routes) }}"
                  :versions="{{ Js::from($versions) }}"></bom-edit>
    </div>
@endsection
