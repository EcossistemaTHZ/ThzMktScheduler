<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class CampaignRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function findAll(): array
    {
        $stmt = $this->db->query(
            'SELECT id, subject, message, scheduled_at, status, created_at FROM campaigns ORDER BY scheduled_at ASC',
        );

        return $stmt->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, subject, message, scheduled_at, status, created_at FROM campaigns WHERE id = :id',
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM campaigns WHERE id = :id');
        $stmt->execute([':id' => $id]);

        return $stmt->fetch() !== false;
    }

    public function create(string $subject, string $message, string $scheduledAt): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO campaigns (subject, message, scheduled_at) VALUES (:subject, :message, :scheduled_at)',
        );
        $stmt->execute([
            ':subject' => $subject,
            ':message' => $message,
            ':scheduled_at' => $scheduledAt,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @param array<string, string> $fields
     */
    public function update(int $id, array $fields): void
    {
        $sets = [];
        $values = [];

        foreach ($fields as $column => $value) {
            $sets[] = sprintf('%s = ?', $column);
            $values[] = $value;
        }

        $values[] = $id;

        $sql = sprintf('UPDATE campaigns SET %s WHERE id = ?', implode(', ', $sets));
        $this->db->prepare($sql)->execute($values);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM campaigns WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
