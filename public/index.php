<?php

declare(strict_types=1);

session_start();

define('ROOT_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = ROOT_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require ROOT_PATH . '/app/Core/helpers.php';

if (isset($_SESSION['user_id'])) {
    \App\Models\User::touchActivity((int) $_SESSION['user_id']);
}

// Work out the base path so the app runs correctly whether it's hit at
// http://localhost/alumni portal/public/ or via a subfolder rewrite.
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', $scriptDir === '/' ? '' : $scriptDir);

$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$requestPath = rawurldecode($requestPath);
$path = $requestPath;
if (BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
    $path = substr($path, strlen(BASE_URL));
}
if ($path === '') {
    $path = '/';
}

$router = require ROOT_PATH . '/routes/web.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $path);
