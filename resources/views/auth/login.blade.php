<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Sign In - useTwit</title>
    @include('app._meta')
    @include('app._css')
</head>
<body class="bg-gradient h-full flex items-center justify-center">
<section class="flex flex-col items-center justify-center w-full max-w-md mx-1 md:mx-auto">
    <div class="w-full bg-white rounded-lg shadow">
        <div class="space-y-4 p-4">
            <h1 class="flex items-center text-xl font-bold text-gray-800">
                <a href="#" class="flex items-center">
                    <img class="w-8 h-8" src="{{ asset('images/logo.svg', true) }}" alt="useTwit">
                </a>
                <span class="ml-2 pt-1.5">Sign in</span>
            </h1>
            <form class="lg:space-y-4"
                  action="{{ route('auth.login') }}"
                  method="post"
                  autocomplete="off"
            >
                @csrf
                <div class="mb-4">
                    @if(count($errors))
                        <div>
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-800">
                        Username
                    </label>
                    <input type="text"
                           name="username"
                           id="username"
                           class="bg-gray-50 border border-gray-300 text-gray-800 rounded-lg focus:ring-teal-500 focus:border-teal-500 focus:outline-teal-400/50 ring-0 outline-offset-0 focus:outline block w-full p-2.5"
                           placeholder="Username"
                           maxlength="255"
                           required
                    >
                </div>
                <div class="mb-4">
                    <label for="password"
                           class="block mb-2 text-sm font-medium text-gray-800 dark:text-white"
                    >
                        Password
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="bg-gray-50 border border-gray-300 text-gray-800 rounded-lg focus:ring-teal-500 focus:border-teal-500 focus:outline-teal-400/50 ring-0 outline-offset-0 focus:outline block w-full p-2.5"
                           placeholder="••••••••"
                           maxlength="255"
                           required
                    >
                </div>
                <button type="submit"
                        class="w-full text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                >
                    Sign in
                </button>
            </form>
        </div>
    </div>
    <div class="text-gray-800 mt-4 text-sm drop-shadow">&copy; 2024 useTwit v{{ config('app.version') }}</div>
</section>
</body>

</html>
