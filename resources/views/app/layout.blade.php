<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    @include('app._css')
    @include('app._meta')
    @include('app._javascript')
</head>

<body>

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

    <div class="flex-1 overflow-x-auto pb-4">

        <header class="mx-4 mt-8 font-bold text-2xl text-slate-700">
            @yield('heading')
        </header>

        @yield('breadcrumbs')

        <main class="p-4 bg-white border-y dark:bg-slate-700 lg:border-x border-gray-200 dark:border-slate-500 mx-0 lg:mx-4">
            @include('app._errors')

            @yield('content')
        </main>

    </div>
</div>

@include('app._footer')

</body>

</html>
