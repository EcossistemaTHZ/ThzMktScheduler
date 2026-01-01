<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private ?PDO $connection = null;

    public function __construct(
        private string $dsn = 'sqlite:' . __DIR__ . '/../../database.sqlite',
        private ?string $username = null,
        private ?string $password = null
    ) {}

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            try {
                $this->connection = new PDO($this->dsn, $this->username, $this->password);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // Ensure foreign keys are enabled for SQLite
                if (str_starts_with($this->dsn, 'sqlite:')) {
                    $this->connection->exec("PRAGMA foreign_keys = ON;");
                }
            } catch (PDOException $e) {
                // In production, log this error and show a generic message
                throw new \RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }

        return $this->connection;
    }

    public function initialize(): void
    {
        $pdo = $this->getConnection();
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS campaigns (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            scheduled_at DATETIME NOT NULL,
            status TEXT DEFAULT 'pending', -- pending, sent, failed
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Destinations implementation (Many-to-Many or simple list? Prompt implies 'destinos de envio', maybe just users?)
        // Let's assume generic destinations for now, or link to users. 
        // Given 'Cadastro de usuários e destinos', let's make a 'destinations' table or just assume Users ARE the destinations.
        // I'll make a linkage table for scalable campaigns.
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS campaign_destinations (
            campaign_id INTEGER,
            user_id INTEGER,
            PRIMARY KEY (campaign_id, user_id),
            FOREIGN KEY(campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
    }
}
