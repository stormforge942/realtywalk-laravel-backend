<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Your password has been reset!',
    'sent' => 'We have e-mailed your password reset link!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that e-mail address.",

    /* UI Reset Password Pages */
    'request' => [
        'meta' => [
            'title' => 'Reset Password &lsaquo;',
        ],
        'title' => 'Reset Your Password',
        'caption' => 'Enter Email to reset password',
        'form' => [
            'placeholders' => [
                'email' => 'Email'
            ],
            'btn_submit' => 'Send password reset link'
        ],
        'link_remember' => 'I remember now, log me in',
    ],
    'reset' => [
        'meta' => [
            'title' => 'New Password &lsaquo; Reset Password &lsaquo;',
        ],
        'title' => 'Reset Password',
        'caption' => 'Enter email and new password',
        'form' => [
            'placeholders' => [
                'email' => 'Email',
                'password' => 'Password',
                'password_confirmation' => 'Confirm Password'
            ],
            'btn_submit' => 'Reset'
        ]
    ],

    /* Email Template */
    'mail' => [
        'reset_password' => [
            'body' => 'Click here to reset your password: :url'
        ]
    ]
];
