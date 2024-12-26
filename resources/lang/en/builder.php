<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Builder Pages Language Lines
    |--------------------------------------------------------------------------
    */

    'index' => [
        'meta' => [
            'title' => 'Builders &lsaquo;'
        ],
        'title' => 'Builders',
        'btn_create' => 'Add New',
        'table' => [
            'columns' => [
                'id' => 'ID #',
                'name' => 'Title',
                'assign_status' => 'Status',
                'updated_at' => 'Last Update',
                'aliases_count' => 'Aliases',
                'properties_count' => 'Associated Properties',
            ]
        ],
        'unmatched_builders' => [
            'title' => 'Unmatched Builders',
            'table' => [
                'columns' => [
                    'name' => 'Name',
                    'builder' => 'Matched Builder',
                    'updated_at' => 'Last Update',
                ]
            ]
        ],
    ],
    'show' => [
        'meta' => [
            'title' => ':builder &lsaquo; &lsaquo; Builders &lsaquo;'
        ],
        'title' => 'Details',
        'btn_back' => 'Back',
        'btn_edit' => 'Edit'
    ],
    'create' => [
        'meta' => [
            'title' => 'Add New &lsaquo; Builders &lsaquo;'
        ],
        'title' => 'Create Builder',
        'saved_message' => 'Builder saved successfully'
    ],
    'edit' => [
        'meta' => [
            'title' => 'Edit &lsaquo; Builders &lsaquo;'
        ],
        'title' => 'Edit Builder',
        'saved_message' => 'Builder updated successfully'
    ],
    'unmatched_edit' => [
        'meta' => [
            'title' => 'Edit &lsaquo; Unmatched Builders &lsaquo;'
        ],
        'title' => 'Edit Unmatched Builder',
        'saved_message' => 'Unmatched builder updated successfully.'
    ],
    'unmatched_labels' => [
        'name' => 'Name:',
        'builder' => 'Assign to Builder:',
        'btn_submit' => 'Save',
        'btn_cancel' => 'Cancel'
    ],
    'labels' => [
        'gallery' => 'Gallery',
        'logo' => 'Logo',
        'name' => 'Name:',
        'slug' => 'Slug:',
        'slug_help' => 'Leave it empty to make the slug based from name.',
        'alias_of' => 'Alias of:',
        'profile_headline' => 'Profile Headline:',
        'descr' => 'Description:',
        'email' => 'Email Address:',
        'phone' => 'Phone:',
        'website' => 'Website:',
        'builder_areas' => 'Builder Areas:',
        'builder_areas_placeholder' => 'Choose builder areas',
        'address' => 'Address',
        'address1' => 'Address 1',
        'address2' => 'Address 2',
        'address3' => 'Address 3',
        'created_at' => 'Created At:',
        'updated_at' => 'Updated At:',
        'btn_submit' => 'Save',
        'btn_cancel' => 'Cancel',
        'documents' => "Documents"
    ]
];
