@extends('admin.layout')

@section('heading')
    Edit Bom Network: {{ $bom->name }}
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.bom-operations-network.edit', $bom) }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/bom-operations-network-edit.js'])
@endsection

@section('content')
    <div id="app">
        <bom-operations-network-edit :routes="{{ Js::from($routes) }}"
                                     :operations="{{ Js::from($operations) }}"
        ></bom-operations-network-edit>
    </div>
@endsection
