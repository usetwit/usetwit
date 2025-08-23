@extends('admin.layout')

@section('heading')
    All BOMs
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.boms.index') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/boms-index.js'])
@endsection

@section('content')
    <div id="app" class="mx-0 lg:mx-4">
        <Index :pagination-settings="{{ Js::from($paginationSettings) }}"
               :date-settings="{{ Js::from($dateSettings) }}"
               :routes="{{ Js::from($routes) }}"></Index>
    </div>
@endsection
