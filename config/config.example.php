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
    // Canonical scheme+host (no trailing slash) used ONLY for links/images inside outbound
    // emails — unlike paths rendered in a browser, an email has no "current page" to resolve a
    // relative URL against. Leave null to fall back to the host of the request that queued the
    // email (fine for a single-domain site); set explicitly if that's ever not reliable, e.g.
    // 'https://alumni.rbsnapps.com'.
    'app_url' => null,
    // SMTP credentials for outbound email (account registration, password resets, in-app
    // notifications). Leave host/username blank to leave mail features inert — queued emails
    // will just sit as 'pending', then flip to 'failed' after repeated attempts, instead of
    // crashing anything.
    'mail' => [
        'host' => '',
        'port' => 587,
        'encryption' => 'tls', // 'tls' or 'ssl'
        'username' => '',
        'password' => '',
        'from_email' => 'noreply@rbsnapps.com',
        'from_name' => 'Rome Business School Nigeria Alumni Portal',
    ],
];
