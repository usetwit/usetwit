<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="overflow-x-hidden w-full">

<head>
    <title>{!! Breadcrumbs::view('app._title') !!}</title>
    @include('app._css')
    @include('app._meta')
    @include('app._javascript')
</head>

<body>
<div id="storage"><storage :version="{{ Js::from($version) }}"></storage></div>

<div id="navbar">
    @include('app._navbar')
</div>

@if(session()->has('success') || count($errors))
    <div id="flash">
        <Flash
            @if(session()->has('success')) type="success" message="{{ addslashes(session('success')) }}"

            @elseif(count($errors)) type="error" message="{{ addslashes($errors->first()) }}" @endif
        />
    </div>
@endif

<div class="bg-slate-100 dark:bg-slate-900 flex">

    <aside id="sidebar" class="flex">
        @include('app._sidebar')
    </aside>

    <div class="flex-1 pb-4 overflow-x-auto">

        <header class="mx-4 mt-8 font-bold text-2xl text-slate-700">
            @yield('heading')
        </header>

        @yield('breadcrumbs')

        <main class="p-4 bg-white border-y dark:bg-slate-700 lg:border-x border-gray-200 dark:border-slate-500 mx-0 lg:mx-4 overflow-x-auto" id="xxx">
            @include('app._errors')

            @yield('content')
        </main>

    </div>
</div>

@include('app._footer')

</body>

</html>
