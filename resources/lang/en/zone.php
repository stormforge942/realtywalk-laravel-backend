<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zone Pages Language Lines
    |--------------------------------------------------------------------------
    */

    'index' => [
        'meta' => [
            'title' => 'Zones &lsaquo; Polygons &lsaquo;'
        ],
        'title' => 'Zones',
        'btn_create' => 'Add New',
        'table' => [
            'columns' => [
                'name' => 'Title',
                'type' => 'Type',
                'lat' => 'Latitude',
                'lng' => 'Longitude'
            ]
        ]
    ],
    'show' => [
        'meta' => [
            'title' => ':zone &lsaquo; Zones &lsaquo; Polygons &lsaquo;'
        ],
        'title' => 'Details',
        'btn_back' => 'Back',
        'btn_edit' => 'Edit'
    ],
    'create' => [
        'meta' => [
            'title' => 'Add New &lsaquo; Zones &lsaquo; Polygons &lsaquo;'
        ],
        'title' => 'Create Zone',
        'saved_message' => 'Zone saved successfully'
    ],
    'edit' => [
        'meta' => [
            'title' => 'Edit &lsaquo; Zones &lsaquo; Polygons &lsaquo;',
        ],
        'title' => 'Edit Zone',
        'saved_message' => 'Zone updated successfully'
    ],
    'labels' => [
        'name' => 'Name:',
        'code' => 'Code:',
        'lat' => 'Latitude:',
        'lng' => 'Longitude:',
        'parent' => 'Parent:',
        'parent_placeholder' => 'Select zone parent (leave it empty if a root)',
        'btn_submit' => 'Save',
        'btn_cancel' => 'Cancel'
    ]
];
