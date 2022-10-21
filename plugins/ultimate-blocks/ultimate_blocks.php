<?php

return [
    'faq' => [
        'type' => 'ultimate',
        'icon' => 'fa fa-feather',
        'color' => '#da2a73',
        'has_statistics' => false,
        'display_dynamic_name' => false,
    ],
    'discord' => [
        'type' => 'ultimate',
        'icon' => 'fab fa-discord',
        'color' => '#7289D9',
        'has_statistics' => false,
        'display_dynamic_name' => false,
    ],
    'facebook' => [
        'type' => 'ultimate',
        'icon' => 'fab fa-facebook',
        'color' => '#4267B2',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['www.facebook.com', 'fb.watch']
    ],
    'reddit' => [
        'type' => 'ultimate',
        'icon' => 'fab fa-reddit',
        'color' => '#FF4500',
        'has_statistics' => false,
        'display_dynamic_name' => false,
        'whitelisted_hosts' => ['www.reddit.com']
    ],
    'audio' => [
        'type' => 'ultimate',
        'icon' => 'fa fa-volume-up',
        'color' => '#003b63',
        'has_statistics' => false,
        'display_dynamic_name' => 'name',
        'whitelisted_file_extensions' => ['mp3', 'm4a']
    ],
    'video' => [
        'type' => 'ultimate',
        'icon' => 'fa fa-video',
        'color' => '#0c3db7',
        'has_statistics' => false,
        'display_dynamic_name' => 'name',
        'whitelisted_file_extensions' => ['mp4', 'webm']
    ],
    'file' => [
        'type' => 'ultimate',
        'icon' => 'fa fa-file',
        'color' => '#a0a0a0',
        'has_statistics' => true,
        'display_dynamic_name' => 'name',
        'whitelisted_file_extensions' => ['pdf', 'zip']
    ],
    'countdown' => [
        'type' => 'ultimate',
        'icon' => 'fa fa-clock',
        'color' => '#2b2b2b',
        'has_statistics' => false,
        'display_dynamic_name' => false,
    ],
    'cta' => [
        'type' => 'ultimate',
        'icon' => 'fa fa-comments',
        'color' => '#3100d6',
        'has_statistics' => true,
        'display_dynamic_name' => 'name',
    ],
    'external_item' => [
        'type' => 'ultimate',
        'icon' => 'fa fa-money-bill-wave',
        'color' => '#00ce18',
        'has_statistics' => true,
        'display_dynamic_name' => 'name',
    ],
    'share' => [
        'type' => 'ultimate',
        'icon' => 'fa fa-share-square',
        'color' => '#00d3ac',
        'has_statistics' => true,
        'display_dynamic_name' => 'name',
    ],
    'youtube_feed' => [
        'type' => 'ultimate',
        'icon' => 'fab fa-youtube',
        'color' => '#282828',
        'has_statistics' => false,
        'display_dynamic_name' => false,
    ],
];
