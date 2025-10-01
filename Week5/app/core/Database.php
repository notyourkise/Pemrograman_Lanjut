<?php

class Database
{
    private static ?mysqli $conn = null;

    public static function getConnection(): mysqli
    {
        if (self::$conn instanceof mysqli) {
            return self::$conn;
        }

        $config = require __DIR__ . '/../config.php';
        $db = $config['db'];

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
            // Set charset
            $conn->set_charset($db['charset'] ?? 'utf8mb4');
        } catch (mysqli_sql_exception $e) {
            http_response_code(500);
            echo 'Database connection error.';
            // For dev: uncomment below
            // echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            exit;
        }

        self::$conn = $conn;
        return self::$conn;
    }
}
