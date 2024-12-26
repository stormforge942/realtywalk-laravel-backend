<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
    'email_is_not_verified' => 'Your account has not been activated yet. Please check your email for the verification link and activate your account before logging in.',

    /* UI Authentication Pages */
    'login' => [
        'meta' => [
            'title' => 'Log In &lsaquo;',
        ],
        'title' => 'Welcome Back to Realty WALK CMS',
        'caption' => 'Sign in to continue to admin page.',
        'form_title' => 'Login Form',
        'form' => [
            'placeholders' => [
                'email' => 'Email Address'
            ],
            'btn_submit' => "Login",
        ],
        'link_forgot_password' => "Forgot password?"
    ],
];
