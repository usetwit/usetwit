<html>
<head>
    <title>Sign In - useTwit</title>
    @include('app._meta')

    @include('app._css')
</head>
<body class="bg-gradient h-full">
<section class="flex flex-col items-center justify-center w-full max-w-md mx-auto mt-16">
    <a href="#" class="flex items-center mb-4 text-2xl font-semibold text-gray-800">
        <img class="w-8 h-8 mr-2" src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg" alt="logo">
        useTwit
    </a>
    <div class="w-full bg-white rounded-lg shadow">
        <div class="space-y-4 p-4">
            <h1 class="text-xl font-bold text-gray-900">
                Sign in to your account
            </h1>
            <form class="space-y-4"
                  action="{{ route('auth.login') }}"
                  method="post"
                  autocomplete="off"
            >
                @csrf
                <div>
                    @if(count($errors))
                        <div class="text-red-500 mb-2">
                            {{ $errors->first() }}
                        </div>
                    @endif
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900">
                        Username
                    </label>
                    <input type="text"
                           name="username"
                           id="username"
                           class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-slate-600 focus:border-slate-600 block w-full p-2.5"
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
                           class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-slate-600 focus:border-slate-600 block w-full p-2.5"
                           placeholder="••••••••"
                           maxlength="255"
                           required
                    >
                </div>
                <button type="submit"
                        class="w-full text-white bg-slate-600 hover:bg-slate-700 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                >
                    Sign in
                </button>
            </form>
        </div>
    </div>
    <div class="text-slate-600 mt-4">useTwit {{ config('app.version') }}</div>
</section>
</body>

</html>
