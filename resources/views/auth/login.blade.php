<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Sign In - useTwit</title>
    @include('app._meta')
    @include('app._css')
</head>
<body class="bg-gradient h-full">
<section class="flex flex-col items-center justify-center w-full max-w-md mx-auto mt-16">
    <a href="#" class="flex items-center mb-2 text-3xl font-semibold text-slate-600">
        <img class="w-20 h-20" src="{{ asset('images/logo.svg', true) }}" alt="useTwit">
    </a>
    <div class="w-full bg-white rounded-lg shadow">
        <div class="space-y-4 p-4">
            <h1 class="text-xl font-bold text-gray-800 mb-4">
                Sign in to your account
            </h1>
            <form class="space-y-8"
                  action="{{ route('auth.login') }}"
                  method="post"
                  autocomplete="off"
            >
                @csrf
                <div>
                    @if(count($errors))
                        <div>
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900">
                        Username
                    </label>
                    <input type="text"
                           name="username"
                           id="username"
                           class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-teal-500 focus:border-teal-500 focus:outline-teal-400/50 ring-0 outline-offset-0 focus:outline block w-full p-2.5"
                           placeholder="Username"
                           maxlength="255"
                           required
                    >
                </div>
                <div>
                    <label for="password"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Password
                    </label>
                    <input type="password"
                           name="password"
                           id="password"
                           class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-teal-500 focus:border-teal-500 focus:outline-teal-400/50 ring-0 outline-offset-0 focus:outline block w-full p-2.5"
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
    <div class="text-teal-500 mt-4 text-sm">&copy; 2024 useTwit v{{ config('app.version') }}</div>
</section>
</body>

</html>
