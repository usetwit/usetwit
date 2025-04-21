<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="overflow-x-hidden w-full">

<head>
    <title>
        @if (count($breadcrumbs))
            @foreach (array_reverse($breadcrumbs->toArray()) as $breadcrumb)
                {{ $breadcrumb->title }} @if(!$loop->last) / @endif
            @endforeach
            - useTwit
        @endif
    </title>

    @vite(['resources/css/app.css'])

    @vite(['resources/js/app.js'])

    @yield('javascript')

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/svg+xml" sizes="any" href="{{ asset('images/logo.svg', true) }}">
    <meta property="og:site_name" content="useTwit">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('images/favicons/apple-icon-57x57.png', true) }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('images/favicons/apple-icon-60x60.png', true) }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/favicons/apple-icon-72x72.png', true) }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/favicons/apple-icon-76x76.png', true) }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('images/favicons/apple-icon-114x114.png', true) }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('images/favicons/apple-icon-120x120.png', true) }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/favicons/apple-icon-144x144.png', true) }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/favicons/apple-icon-152x152.png', true) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicons/apple-icon-180x180.png', true) }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicons/android-icon-192x192.png', true) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicons/favicon-32x32.png', true) }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicons/favicon-96x96.png', true) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicons/favicon-16x16.png', true) }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicons/favicon.ico', true) }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('images/favicons/ms-icon-144x144.png', true) }}">
    <meta name="theme-color" content="#1e293b">


@if(session()->has('success') || count($errors))
        @vite(['resources/js/app/flash.js'])
    @endif
</head>

<body>
<div id="storage">
    <storage version="{{ config('app.version') }}"></storage>
</div>

<div id="navbar" class="sticky top-0 z-200">
    <navbar logout-route="{{ route('auth.logout') }}"
            logo="{{ asset('images/logo.svg', true) }}"
            default-profile-image="{{ asset('images/user/profile/profile_default.svg', true) }}"
            user-profile-image="{{ $userProfileImage }}"
            name="{{ $name }}"
    ></navbar>
</div>


@if(session()->has('success') || count($errors))
    <div id="flash">
        <Flash @if(session()->has('success')) type="success" message="{{ addslashes(session('success')) }}"
               @elseif(count($errors)) type="error" message="{{ addslashes($errors->first()) }}" @endif
        ></Flash>
    </div>
@endif

<div class="bg-slate-100 dark:bg-slate-700 flex">

    <aside id="sidebar" class="flex">
        <sidebar :routes="{{ Js::from($routes) }}"
                 :uris="{{ Js::from($uris) }}"
                 current="{{ $current }}"
        ></sidebar>
    </aside>

    <div class="flex-1 pb-4 overflow-x-auto">

        <header class="mx-2 mt-4 lg:mx-4 lg:mt-6 font-bold text-2xl text-slate-700 dark:text-white flex align-middle">
            @yield('heading')
        </header>

        @yield('breadcrumbs')

        <main class="overflow-x-auto">
            @if(count($errors))
                <div class="flex p-4 mb-4 text-sm text-red-500 rounded-lg bg-red-50 border border-red-500" role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Danger</span>
                    <div>
                        <span class="font-medium">Ensure that these requirements are met:</span>
                        <ul class="mt-1.5 list-disc list-inside">
                            @foreach ( $errors->all() AS $error )
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

<footer class="bg-slate-800 py-8 border-t border-slate-700">
    <div class="max-w-6xl mx-auto text-white">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="space-y-2">
                <a href="#home" class="block hover:underline">Home</a>
                <a href="#about" class="block hover:underline">About</a>
                <a href="#services" class="block hover:underline">Services</a>
                <a href="#contact" class="block hover:underline">Contact</a>
            </div>
            <div class="space-y-2">
                <a href="https://facebook.com" target="_blank" aria-label="Facebook" class="block hover:underline">Facebook</a>
                <a href="https://twitter.com" target="_blank" aria-label="Twitter" class="block hover:underline">Twitter</a>
                <a href="https://instagram.com" target="_blank" aria-label="Instagram" class="block hover:underline">Instagram</a>
            </div>
            <div>
                <p class="text-sm">Contact us: <a href="mailto:info@example.com" class="hover:underline">info@example.com</a></p>
            </div>
        </div>
    </div>
</footer>


</body>
</html>
