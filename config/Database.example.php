<?php
// ============================================================
// config/Database.php
// Singleton PDO database connection
// ============================================================

namespace Config;

use PDO;
use PDOException;

class Database
{
    // ── Connection settings ──────────────────────────────────
private $host = "localhost";
private $db_name = "student_management";
private $username = "your_username";
private $password = "your_password";
    // Singleton instance
    private static ?Database $instance = null;

    // Cached PDO connection
    private ?PDO $connection = null;

    // Private constructor prevents direct instantiation
    private function __construct() {}

    // ── Singleton accessor ───────────────────────────────────
    /**
     * Returns the single Database instance.
     */
    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    // ── PDO connection ───────────────────────────────────────
    /**
     * Returns a live PDO connection, creating it on first call.
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                // In production, log this and show a generic error
                die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
            }
        }

        return $this->connection;
    }

    // Prevent cloning and unserialization of the singleton
    private function __clone() {}
    public function __wakeup(): void {}
}
