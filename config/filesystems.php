<?php

$isProduction = env('APP_ENV') === 'production';

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver'                  => 's3',
            'key'                     => $isProduction ? env('AWS_ACCESS_KEY_ID') : env('AWS_ACCESS_KEY_ID_LOCAL'),
            'secret'                  => $isProduction ? env('AWS_SECRET_ACCESS_KEY') : env('AWS_SECRET_ACCESS_KEY_LOCAL'),
            'region'                  => $isProduction ? env('AWS_REGION') : env('AWS_DEFAULT_REGION_LOCAL'),
            'bucket'                  => $isProduction ? env('AWS_BUCKET') : env('AWS_BUCKET_LOCAL'),
            'url'                     => $isProduction ? env('AWS_URL') : env('AWS_URL_LOCAL'),
            'endpoint'                => $isProduction ? env('AWS_ENDPOINT') : env('AWS_ENDPOINT_LOCAL'),
            'use_path_style_endpoint' => $isProduction ? env('AWS_USE_PATH_STYLE_ENDPOINT', true) : env('AWS_USE_PATH_STYLE_ENDPOINT_LOCAL', true),
            'throw'                   => true,
            'report'                  => false,
        ],

        's3_temp' => [
            'driver'                  => 's3',
            'key'                     => $isProduction ? env('AWS_ACCESS_KEY_ID') : env('AWS_ACCESS_KEY_ID_LOCAL'),
            'secret'                  => $isProduction ? env('AWS_SECRET_ACCESS_KEY') : env('AWS_SECRET_ACCESS_KEY_LOCAL'),
            'region'                  => $isProduction ? env('AWS_REGION') : env('AWS_DEFAULT_REGION_LOCAL'),
            'bucket'                  => $isProduction ? env('AWS_BUCKET_TMP') : env('AWS_BUCKET_TEMP_LOCAL'),
            'url'                     => $isProduction ? env('AWS_URL') : env('AWS_URL_TEMP_LOCAL', env('AWS_URL_LOCAL')),
            'endpoint'                => $isProduction ? env('AWS_ENDPOINT') : env('AWS_ENDPOINT_LOCAL'),
            'use_path_style_endpoint' => $isProduction ? env('AWS_USE_PATH_STYLE_ENDPOINT', true) : env('AWS_USE_PATH_STYLE_ENDPOINT_LOCAL', true),
            'throw'                   => true,
            'report'                  => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
