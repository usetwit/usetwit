<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="overflow-x-hidden w-full">

<head>
    <title>{!! Breadcrumbs::view('app._title') !!}</title>
    @include('app._css')
    @include('app._meta')
    @include('app._javascript')

    @if(session()->has('success') || count($errors))
        @vite(['resources/js/app/flash.js'])
    @endif
</head>

<body>
<div id="storage"><storage version="{{ config('app.version') }}"></storage></div>

@include('app._navbar')

@if(session()->has('success') || count($errors))
<div id="flash">
    <Flash @if(session()->has('success')) type="success" message="{{ addslashes(session('success')) }}"
           @elseif(count($errors)) type="error" message="{{ addslashes($errors->first()) }}"
           @else type="success" message="hello world" @endif
    ></Flash>
</div>
@endif

<div class="bg-slate-100 dark:bg-slate-900 flex">

    <aside id="sidebar" class="flex">
        @include('app._sidebar')
    </aside>

    <div class="flex-1 pb-4 overflow-x-auto">

        <header class="mx-2 mt-4 lg:mx-4 lg:mt-8 font-bold text-2xl text-slate-700">
            @yield('heading')
        </header>

        @yield('breadcrumbs')

        <main
            class="bg-white border-y dark:bg-slate-700 lg:border-x border-gray-200 dark:border-slate-500 mx-0 lg:mx-4 overflow-x-auto"
        >
            @include('app._errors')

            @yield('content')
        </main>

    </div>
</div>

@include('app._footer')

</body>
</html>
