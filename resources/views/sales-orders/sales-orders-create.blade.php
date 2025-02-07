@extends('app.layout')

@section('heading')
    Create New Sales Order
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('sales-orders.create') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/sales-orders-create.js'])
@endsection

@section('content')
    <div id="app">
        <sales-orders-create route-store="{{ route('sales-orders.store') }}"
                             route-stock-bom-search="{{ route('sales-orders.stock-bom-search') }}"
                             route-redirect="{{ route('sales-orders.index') }}"
                             :date-settings="{{ Js::from($dateSettings) }}"
        ></sales-orders-create>
    </div>
@endsection
