<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Page Language Lines
    |--------------------------------------------------------------------------
    */

    'summary' => [
        'title' => 'Data Summary',
        'properties' => 'Properties',
        'builders' => 'Builders',
        'polygons' => 'Polygons',
        'users' => 'Users (Admin/Regular)'
    ],
    'total_properties_per_category' => 'Total Properties per Category',
    'total_properties_per_style' => 'Total Properties per Style',
    'last_registered_users' => [
        'title' => 'Last Registered Users',
        'table' => [
            'columns' => [
                'user_name' => 'User',
                'activity' => 'Activity'
            ],
            'rows' => [
                'registered_at' => 'Registered at: :datetime',
                'last_login_at' => 'Last Login'
            ]
        ]
    ]
];
