<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class Migration
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->createMigrationsTable();
    }

    /**
     * Create the migrations table if it doesn't exist.
     */
    private function createMigrationsTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Run all pending migrations.
     */
    public function migrate(): void
    {
        // Get list of applied migrations
        $applied = $this->db->query("SELECT name FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
        
        // Get all migration files
        $files = glob(dirname(__DIR__, 2) . '/database/migrations/*.sql');
        
        foreach ($files as $file) {
            $name = basename($file);
            
            // Skip if already applied
            if (in_array($name, $applied)) {
                continue;
            }
            
            // Run migration
            $sql = file_get_contents($file);
            $this->db->exec($sql);
            
            // Record migration
            $stmt = $this->db->prepare("INSERT INTO migrations (name) VALUES (?)");
            $stmt->execute([$name]);
            
            echo "Applied migration: {$name}\n";
        }
    }
}
