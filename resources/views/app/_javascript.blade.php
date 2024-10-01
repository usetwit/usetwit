@yield('javascript')

@if(session()->has('success') || count($errors))
    @vite(['resources/js/flash.js'])
@endif
