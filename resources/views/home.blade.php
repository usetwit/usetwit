@extends('app.layout')

@section('heading')
    Home
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('home') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/home.js'])
@endsection

@section('content')
    <div id="app">
        <home :display-format="{{ Js::from($displayFormat) }}"
              :format="{{ Js::from($format) }}"
              :regex="{{ Js::from($regex) }}"
              :separator="{{ Js::from($separator) }}"
        ></home>
    </div>
@endsection
