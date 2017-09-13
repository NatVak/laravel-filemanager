<?php

/*
  |--------------------------------------------------------------------------
  | File Manager Configurations
  |--------------------------------------------------------------------------
  |
  */

return [

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | When active, client's calls (ex: getting image alts for images) will be
    | cached using laravel's default cache.
    */
    'use_cache' => true,

    /*
    |--------------------------------------------------------------------------
    | App layouts
    |--------------------------------------------------------------------------
    |
    | This value holds the app layouts for the several states:
    | Main Layout - the main app layout.
    | Popup Layout - a minimalist layout for the popup mode.
    */
    'layouts' => [
        'main' => 'FileManager::file-manager.layout',
        'popup' => 'FileManager::file-manager.layout',
    ],

    /*
    |--------------------------------------------------------------------------
    | Images Crop sizes
    |--------------------------------------------------------------------------
    |
    | This value holds the images sizes to auto crop images uploaded to
    | the file manager.
    */
    'crop' => [
        'medium' => [
            'display_name' => 'Medium',
            'height' => 150,
            'width' => 100,
        ],
        'thumbnail' => [
            'display_name' => 'Small',
            'height' => 120,
            'width' => 120,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed extensions
    |--------------------------------------------------------------------------
    |
    | This value holds default allowed extensions for uploading files.
    |
    */
    'allowed_extensions' => [
        'gif', 'jpeg', 'png', 'jpg', 'pneg', 'bmp', 'TIFF', 'mp4', 'mp3',
        'avi', 'mpeg', 'psd', 'pdf', 'docx', 'doc', 'xls', 'csv', 'xlsx', 'ppt', 'pptx', 'txt', 'gif', 'zip',
    ],

    /*
     |--------------------------------------------------------------------------
     | Max file size
     |--------------------------------------------------------------------------
     |
     | This value holds default allowed max file size in MB
     |
     */
    'max_file_size' => 10,


    /*
     |--------------------------------------------------------------------------
     | Crop inputs names
     |--------------------------------------------------------------------------
     |
     | This array holds the custom crops for image inputs
     |
     */
    'custom_crops' => [
        'users_images' => [
            'listing_xs' => [
                'media_query' => [
                    'min' => '0px',
                ],
                'title' => 'Listing XS',
                'width' => 377,
                'height' => 150,
            ],
            'listing_sm' => [
                'media_query' => [
                    'min' => '480px',
                ],
                'title' => 'Listing SM',
                'width' => 307,
                'height' => 300,
            ],
            'listing_md' => [
                'media_query' => [
                    'min' => '750px',
                    'max' => '1040px'
                ],
                'title' => 'Listing MD',
                'width' => 377,
                'height' => 300,
            ],
            'listing_lg' => [
                'media_query' => [
                    'min' => '1040px',
                ],
                'title' => 'Listing LG',
                'width' => 277,
                'height' => 300,
            ],
            'user_profile' => [
                'title' => 'User Profile',
                'width' => 1020,
                'height' => 463,
            ],
        ],
    ],
];