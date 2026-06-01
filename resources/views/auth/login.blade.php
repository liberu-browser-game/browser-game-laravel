@extends('layouts.app')

@section('content')
    <div class="min-h-full flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Please sign in to access the admin panel.') }}
            </div>
        
            <x-validation-errors class="mb-4" />
        
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label class="block font-medium text-sm text-gray-700" for="email">
                        {{ __('Email') }}
                    </label>
                    <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" id="email" type="email" name="email" required="required" autofocus="autofocus">
                </div>

                <div class="mt-4">
                    <label class="block font-medium text-sm text-gray-700" for="password">
                        {{ __('Password') }}
                    </label>
                    <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" id="password" type="password" name="password" required="required" autocomplete="current-password">
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="remember_me" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a href="/forgot-password" class="underline text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Forgot password?') }}
                    </a>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 ml-4">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>

            @if (\JoelButcher\Socialstream\Socialstream::show())
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <p class="text-xs text-center text-gray-500 mb-3">{{ config('socialstream.prompt', __('Or continue with')) }}</p>

                    <div class="grid grid-cols-3 gap-2">
                        @foreach (\JoelButcher\Socialstream\Socialstream::providers() as $provider)
                            <a href="{{ route('oauth.redirect', ['provider' => $provider['id']]) }}"
                               class="flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 transition ease-in-out duration-150"
                               title="{{ __('Login with :provider', ['provider' => $provider['name']]) }}">
                                {{ $provider['buttonLabel'] ?? $provider['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
