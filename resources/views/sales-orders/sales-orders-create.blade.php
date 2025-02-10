@extends('admin.layout')

@section('heading')
    Create New Sales Order
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.sales-orders.create') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/sales-orders-create.js'])
@endsection

@section('content')
    <div id="app">
        <sales-orders-create route-store="{{ route('admin.sales-orders.store') }}"
                             route-stock-bom-search="{{ route('admin.sales-orders.stock-bom-search') }}"
                             route-redirect="{{ route('admin.sales-orders.index') }}"
                             :date-settings="{{ Js::from($dateSettings) }}"
        ></sales-orders-create>
    </div>
@endsection
