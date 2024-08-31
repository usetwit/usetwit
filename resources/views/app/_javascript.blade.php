@vite([
    'resources/js/app.js',
    'resources/js/sidebar.js',
    'resources/js/navbar.js',
])

@yield('javascript')

@if(session()->has('success') || count($errors))
    @vite(['resources/js/flash.js'])
@endif
