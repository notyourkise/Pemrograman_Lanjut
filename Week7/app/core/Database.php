<?php
class Database
{
    private static ?mysqli $conn = null;
    public static function getConnection(): mysqli
    {
        if (self::$conn instanceof mysqli) return self::$conn;
        $config = require __DIR__ . '/../config.php';
        $db = $config['db'];
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
        $conn->set_charset($db['charset'] ?? 'utf8mb4');
        return self::$conn = $conn;
    }
    public static function beginTransaction(): void { self::getConnection()->begin_transaction(); }
    public static function commit(): void { self::getConnection()->commit(); }
    public static function rollBack(): void { self::getConnection()->rollback(); }
}
