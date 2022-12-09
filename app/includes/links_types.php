<?php

return [
    'link' => [
        'icon' => 'fa fa-link',
        'color' => '#38b2ac',
    ],
    'biolink' => [
        'icon' => 'fa fa-fw fa-hashtag',
        'color' => '#383eb2',
    ],
    'biolink_template' => [
        'icon' => 'fa fa-fw fa-hashtag',
        'color' => '#383eb2',
    ],
    'file' => [
        'icon' => 'fa fa-file',
        'color' => '#8c8c8c',
    ],
    'vcard' => [
        'icon' => 'fa fa-id-card',
        'color' => '#FAB005',

        'fields' => [
            'first_name' => [
                'max_length' => 64,
            ],
            'last_name' => [
                'max_length' => 64,
            ],
            'phone' => [
                'max_length' => 32,
            ],
            'email' => [
                'max_length' => 320,
            ],
            'url' => [
                'max_length' => 1024,
            ],
            'company' => [
                'max_length' => 64,
            ],
            'job_title' => [
                'max_length' => 64,
            ],
            'birthday' => [
                'max_length' => 16,
            ],
            'street' => [
                'max_length' => 128,
            ],
            'city' => [
                'max_length' => 64,
            ],
            'zip' => [
                'max_length' => 32,
            ],
            'region' => [
                'max_length' => 32,
            ],
            'country' => [
                'max_length' => 32,
            ],
            'note' => [
                'max_length' => 256,
            ],
            'social_label' => [
                'max_length' => 32
            ],
            'social_value' => [
                'max_length' => 1024
            ]
        ]
    ],
];
