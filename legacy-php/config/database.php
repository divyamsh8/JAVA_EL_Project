<?php
/**
 * Database Connection & PDO Provider
 * Supports MySQL (Default for XAMPP) with seamless SQLite local auto-fallback
 */

declare(strict_types=1);

require_once __DIR__ . '/app.php';

class Database {
    private static ?PDO $pdo = null;
    private static string $driverUsed = 'mysql';

    public static function getConnection(): PDO {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // MySQL Credentials (Standard XAMPP default)
        $mysqlHost = getenv('DB_HOST') ?: '127.0.0.1';
        $mysqlPort = getenv('DB_PORT') ?: '3306';
        $mysqlDb   = getenv('DB_NAME') ?: 'portal_demo';
        $mysqlUser = getenv('DB_USER') ?: 'root';
        $mysqlPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

        // Check if forced driver is specified
        $forcedDriver = getenv('DB_DRIVER'); // 'mysql' or 'sqlite'

        if ($forcedDriver !== 'sqlite') {
            try {
                $dsn = "mysql:host={$mysqlHost};port={$mysqlPort};dbname={$mysqlDb};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];

                self::$pdo = new PDO($dsn, $mysqlUser, $mysqlPass, $options);
                self::$pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
                self::$driverUsed = 'mysql';
                return self::$pdo;
            } catch (PDOException $e) {
                // If MySQL is not reachable and SQLite is allowed as fallback, switch smoothly
                if ($forcedDriver === 'mysql') {
                    throw new RuntimeException("MySQL connection failed: " . $e->getMessage());
                }
            }
        }

        // Fallback to SQLite (zero-config, instant local run)
        try {
            $sqlitePath = __DIR__ . '/../database/portal_demo.sqlite';
            $isNewDb = !file_exists($sqlitePath);

            $dsn = "sqlite:" . $sqlitePath;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$pdo = new PDO($dsn, null, null, $options);
            self::$pdo->exec("PRAGMA foreign_keys = ON;");
            self::$driverUsed = 'sqlite';

            // Auto-bootstrap SQLite if newly created
            if ($isNewDb || filesize($sqlitePath) === 0) {
                require_once __DIR__ . '/../database/init_db.php';
                initialize_sqlite_db(self::$pdo);
            }

            return self::$pdo;
        } catch (PDOException $e) {
            die("Database initialization error: " . $e->getMessage());
        }
    }

    public static function getDriver(): string {
        if (self::$pdo === null) {
            self::getConnection();
        }
        return self::$driverUsed;
    }
}

// Global helper function for shorthand PDO retrieval
function get_db(): PDO {
    return Database::getConnection();
}
