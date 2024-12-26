<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Pages Language Lines
    |--------------------------------------------------------------------------
    */

    'index' => [
        'meta' => [
            'title' => 'Users &lsaquo;',
        ],
        'title' => 'Users',
        'btn_create' => 'Add New',
        'table' => [
            'columns' => [
                'name' => 'Name',
                'role' => 'Role',
                'email' => 'Email Address',
                'created_at' => 'Registered At'
            ]
        ]
    ],
    'show' => [
        'meta' => [
            'title' => ':user &lsaquo; Users &lsaquo;'
        ],
        'title' => 'Details',
        'btn_back' => 'Back',
        'btn_edit' => 'Edit'
    ],
    'create' => [
        'meta' => [
            'title' => 'Add New &lsaquo; Users &lsaquo;'
        ],
        'title' => 'Create User',
        'saved_message' => 'User saved successfully'
    ],
    'edit' => [
        'meta' => [
            'title' => 'Edit &lsaquo; Users &lsaquo;',
        ],
        'title' => 'Edit User',
        'saved_message' => 'User updated successfully'
    ],
    'labels' => [
        'name' => 'Name:',
        'email' => 'Email Address:',
        'role' => 'Role:',
        'builder' => 'Assign Builder:',
        'password' => 'Password:',
        'password_confirmation' => 'Confirm Password:',
        'registered_at' => 'Registered At:',
        'last_login_at' => 'Last Login At:',
        'btn_submit' => 'Save',
        'btn_cancel' => 'Cancel'
    ]
];
