<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/config.php';
            $db = $config['db'];
            $port = isset($db['port']) && $db['port'] !== '' ? ';port=' . $db['port'] : '';
            $dsn = "mysql:host={$db['host']}{$port};dbname={$db['name']};charset={$db['charset']}";

            try {
                self::$instance = new PDO($dsn, $db['user'], $db['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                // Keep MySQL's NOW()/CURRENT_TIMESTAMP in step with PHP's timezone — the two default
                // to different system timezones in this environment, which silently threw off any
                // timestamp comparison mixing a PHP-computed time with a MySQL-computed one (e.g.
                // "is this user online in the last 5 minutes").
                self::$instance->exec("SET time_zone = '" . date('P') . "'");
            } catch (PDOException $e) {
                http_response_code(500);
                die('Database connection failed. Make sure MySQL is running in XAMPP and the "alumni_portal" database has been imported from database/schema.sql.');
            }
        }

        return self::$instance;
    }
}
