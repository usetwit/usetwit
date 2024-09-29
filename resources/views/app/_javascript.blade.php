@vite([
    'resources/js/app/sidebar.js',
    'resources/js/app/navbar.js',
    'resources/js/app/storage.js',
])

@yield('javascript')

@if(session()->has('success') || count($errors))
    @vite(['resources/js/flash.js'])
@endif
