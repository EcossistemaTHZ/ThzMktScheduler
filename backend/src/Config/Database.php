<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private ?PDO $connection = null;

    /**
     * @param string $dsn Pode ser sobrescrito via env DB_DSN
     * @param string|null $username
     * @param string|null $password
     */
    public function __construct(
        private string $dsn = '',
        private ?string $username = null,
        private ?string $password = null
    ) {
        if ($dsn === '') {
            $dsn = getenv('DB_DSN') ?: 'sqlite:' . __DIR__ . '/../../database.sqlite';
            $this->dsn = $dsn;
        }
    }
    
    /**
     * Returns the database connection
     * Retorna a conexão com o banco de dados
     * @return PDO
     */
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
    
    /**
     * Initializes the database
     * Cria as tabelas do banco de dados
     * @return void
     */
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
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS campaign_destinations (
            campaign_id INTEGER,
            user_id INTEGER,
            PRIMARY KEY (campaign_id, user_id),
            FOREIGN KEY(campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )");
    }
}
