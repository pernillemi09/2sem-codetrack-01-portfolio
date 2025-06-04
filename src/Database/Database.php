<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    /**
     * Get the database connection.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $path = dirname(__DIR__, 2) . '/database/database.sqlite';
            $directory = dirname($path);

            // Create database directory if it doesn't exist
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            try {
                self::$connection = new PDO('sqlite:' . $path);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // Enable foreign keys
                self::$connection->exec('PRAGMA foreign_keys = ON');
            } catch (PDOException $e) {
                throw new \RuntimeException(
                    "Could not connect to the SQLite database: {$e->getMessage()}"
                );
            }
        }

        return self::$connection;
    }
}
