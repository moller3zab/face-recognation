<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;

    private function __construct()
    {
        $dbConfig = Config::get('database');
        $host = $dbConfig['host'] ?? '127.0.0.1';
        $port = (int) ($dbConfig['port'] ?? 3306);
        $dbname = $dbConfig['dbname'] ?? 'face_attendance';
        $charset = $dbConfig['charset'] ?? 'utf8mb4';
        $username = $dbConfig['username'] ?? 'root';
        $password = $dbConfig['password'] ?? '';

        $options = $dbConfig['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Try primary configured port first
        try {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbname, $charset);
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            // Try fallback port (3306 <-> 3307) if connection was refused
            $fallbackPort = ($port === 3307) ? 3306 : 3307;
            try {
                $dsnFallback = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $fallbackPort, $dbname, $charset);
                $this->pdo = new PDO($dsnFallback, $username, $password, $options);
            } catch (PDOException $fallbackException) {
                Logger::error("Database Connection Failure on primary port ($port) and fallback port ($fallbackPort): " . $e->getMessage());
                throw new PDOException("Database connection failed. Please ensure MySQL/MariaDB service is started in XAMPP.");
            }
        }

    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
