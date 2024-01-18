<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        's3' => [
            // 'driver' => 's3',
            // 'key' => 'AKIARWTLRBVMHZER73GG',
            // 'secret' => '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',
            // 'region' => 'us-east-1',
            // 'bucket' => 'essentials-uploads',
            // 'url' => env('AWS_URL', 'https://essentials-uploads.s3.amazonaws.com/'),
            // 'visibility' => 'public',
            'driver' => 's3',
            'key' => 'AKIAWJKPA6LX3W7VLIMM',
            'secret' => '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',
            'region' => 'sa-east-1',
            'bucket' => 'uploads-mawa',
            'url' => env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/'),
            'visibility' => 'public',
        ],

        

        'upimgprod' => [
            'driver' => 'local',
            'root'   => public_path() . '/imgproduto',
        ],

        'imgoriginalproduto' => [
            'driver' => 'local',
            'root'   => public_path() . '/imgoriginalproduto',
        ],

        

                  'digitalocean' => [
                    
                    'driver' => 's3',
                    'key' => env('DIGITALOCEAN_SPACES_KEY'), 
                    'secret' => env('DIGITALOCEAN_SPACES_SECRET'),
                    'region' => env('DIGITALOCEAN_SPACES_REGION'),
                    'bucket' => env('DIGITALOCEAN_SPACES_BUCKET'), 
                    'url' => env('SPACEDIG' , 'https://imagemprojectdrop.sfo3.cdn.digitaloceanspaces.com'),
                    'endpoint' => env('SPACES_ENDPOINT', 'https://imagemprojectdrop.sfo3.digitaloceanspaces.com'),
                    'bucket_endpoint' => true,                    
                    'visibility' => 'public',
                    'acl' => 'public-read',
                    
                ],          

    ],

];
