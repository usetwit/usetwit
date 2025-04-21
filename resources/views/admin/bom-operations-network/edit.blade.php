@extends('admin.layout')

@section('heading')
    Edit Network: {{ $bomVersion->bom->name }} v{{ $bomVersion->version }}
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.boms-operations-network.edit', $bomVersion) }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/boms-edit.js'])
@endsection

@section('content')
    <div id="app">
        <bom-operations-network-edit :routes="{{ Js::from($routes) }}"
                                     :operations="{{ Js::from($operations) }}"
        ></bom-operations-network-edit>
    </div>
@endsection
