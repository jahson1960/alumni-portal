<?php

return [
    'app_name' => 'Rome Business School Nigeria - Alumni Network',
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'alumni_portal',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'upload_dir' => __DIR__ . '/../assets/uploads',
    // Set both of these to move uploads to a host outside the deployed project directory
    // (e.g. a dedicated subdomain), so they survive every future deploy untouched:
    //   'upload_dir' => '/home/<user>/domains/uploads.example.com/public_html',
    //   'upload_base_url' => 'https://uploads.example.com',
    // Leave upload_base_url null to keep serving uploads from this app's own assets/uploads/.
    'upload_base_url' => null,
];
