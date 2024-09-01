@extends('app.layout')

@section('heading')
    Home
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('home') }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/home.js'])
@endsection

@section('content')
    <div id="home">

        <form>

            <home></home>

        </form>
    </div>
@endsection
