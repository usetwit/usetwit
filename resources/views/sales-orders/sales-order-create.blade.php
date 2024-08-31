@extends('app.layout')

@section('heading')
    Create New Sales Order
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('sales-orders.create') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/sales-orders-create.js'])
@endsection

@section('content')
    <div id="sales-order-create">

        <form>

            <sales-order-create route-store="{{ route('sales-orders.store') }}"
                                route-stock-bom-search="{{ route('sales-orders.stock-bom-search') }}"
                                route-redirect="{{ route('sales-orders.index') }}"
                                date-format="{{ $date_format }}"
            ></sales-order-create>

        </form>
    </div>
@endsection
