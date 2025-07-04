<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'students'), // CHANGED: Points to students for password resets
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | which utilizes session storage plus the Eloquent user provider.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. Typically, Eloquent is utilized.
    |
    | Supported: "session"
    |
    */

    'guards' => [
        // The 'web' guard is the default for browser sessions. We will make it use the 'students' provider.
        'web' => [
            'driver' => 'session',
            'provider' => 'students', // **THE MAIN FIX IS HERE**
        ],

        // You have a 'students' guard, but 'web' is the standard one to use for the main user type.
        // We will keep it but our controller will use 'web' for students.
        'students' => [
            'driver' => 'session',
            'provider' => 'students',
        ],

        'admins' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'depts' => [
            'driver' => 'session',
            'provider' => 'depts',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application.
    |
    */

    'providers' => [
        // We are removing the default 'users' provider because you don't use a 'users' table or App\Models\User.
        // This prevents the "Unknown column 'email' in 'where clause' on table 'users'" error.
        /*
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        */

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'depts' => [
            'driver' => 'eloquent',
            'model' => App\Models\Dept::class,
        ],

        'students' => [
            'driver' => 'eloquent',
            'model' => App\Models\Student::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    */

    'passwords' => [
        // We rename 'users' to 'students' to match our provider, making it clear.
        'students' => [
            'provider' => 'students',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
        // You can add providers for 'admins' and 'depts' here if they need password reset functionality.
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens', // Can share the table or have a separate one
            'expire' => 60,
        ],
        'depts' => [
            'provider' => 'depts',
            'table' => 'password_reset_tokens',
            'expire' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
