<?php

/**
 * Week 8: Database Class
 * Singleton pattern for database connection
 */

class Database
{
    private static $instance = null;
    private $connection;
    private $config;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->config = require __DIR__ . '/../config.php';
        $this->connect();
    }

    /**
     * Get the singleton instance
     * 
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connect to database
     */
    private function connect()
    {
        $db = $this->config['database'];
        
        try {
            $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";
            $this->connection = new PDO($dsn, $db['username'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get the database connection
     * 
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
    
    /**
     * Execute a query and return results
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return array Results
     */
    public function query(string $query, array $params = []): array
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Query error: " . $e->getMessage());
            throw new Exception("Database query failed");
        }
    }
    
    /**
     * Execute a query without returning results
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return bool Success status
     */
    public function execute(string $query, array $params = []): bool
    {
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Execute error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Insert a record and return the last insert ID
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return int|false Last insert ID or false on failure
     */
    public function insert(string $query, array $params = [])
    {
        try {
            $stmt = $this->connection->prepare($query);
            if (!$stmt->execute($params)) {
                error_log("Insert failed - Execute returned false");
                throw new Exception("Failed to execute insert query");
            }
            $lastId = $this->connection->lastInsertId();
            if (!$lastId) {
                error_log("Insert failed - lastInsertId returned: " . var_export($lastId, true));
            }
            return $lastId;
        } catch (PDOException $e) {
            error_log("Insert error: " . $e->getMessage() . " | Query: " . $query);
            throw new Exception("Database insert failed: " . $e->getMessage());
        }
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
