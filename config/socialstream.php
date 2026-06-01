<?php

use JoelButcher\Socialstream\Features;
use JoelButcher\Socialstream\Providers;

return [

    /*
    |--------------------------------------------------------------------------
    | Socialstream Guard
    |--------------------------------------------------------------------------
    |
    | This configuration value informs Socialstream which authentication
    | guard it should use when authenticating users. This value should
    | correspond with one of your guards in your "auth" configuration.
    |
    */

    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Socialstream Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware Socialstream assigns to its OAuth routes.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | OAuth Providers
    |--------------------------------------------------------------------------
    |
    | Here you may specify the OAuth providers your application supports.
    | Twitter OAuth 1.0 is excluded as it requires live API keys even for
    | the redirect flow.
    |
    */

    'providers' => [
        Providers::bitbucket(),
        Providers::facebook(),
        Providers::github(),
        Providers::gitlab(),
        Providers::google(),
        Providers::linkedin(),
        Providers::linkedinOpenId(),
        Providers::slack(),
        Providers::twitterOAuth2(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Socialstream Features
    |--------------------------------------------------------------------------
    |
    | Some of Socialstream's features are optional. You may disable them
    | by removing them from this array.
    |
    */

    'features' => [
        Features::rememberSession(),
        Features::refreshOAuthTokens(),
        Features::createAccountOnFirstLogin(),
        Features::generateMissingEmails(),
        Features::globalLogin(),
        // Features::authExistingUnlinkedUsers(),
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt
    |--------------------------------------------------------------------------
    |
    | The text displayed above the OAuth provider buttons on login/register pages.
    |
    */

    'prompt' => 'Or Login Via',

    /*
    |--------------------------------------------------------------------------
    | Home Path
    |--------------------------------------------------------------------------
    |
    | The path users are redirected to after a successful OAuth login.
    |
    */

    'home' => '/dashboard',

    /*
    |--------------------------------------------------------------------------
    | Socialstream Redirects
    |--------------------------------------------------------------------------
    |
    | Here you may specify the redirects for various Socialstream actions.
    |
    */

    'redirects' => [
        'login' => '/dashboard',
        'register' => '/dashboard',
        'login-failed' => '/login',
        'registration-failed' => '/register',
        'provider-linked' => '/user/profile',
        'provider-link-failed' => '/user/profile',
    ],

];
