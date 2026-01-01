<?php

declare(strict_types=1);

namespace App\Controller;

class CampaignController extends BaseController
{
    /**
     * Lista todas as campanhas
     * @return void
     */
    public function index(): void
    {
        $stmt = $this->db->query("SELECT * FROM campaigns ORDER BY scheduled_at ASC");
        $campaigns = $stmt->fetchAll();
        $this->jsonResponse($campaigns);
    }

    /**
     * Cria uma campanha
     * @return void
     */
    public function create(): void
    {
        $data = $this->getInput();

        // Validation (Basic)
        if (empty($data['subject']) || empty($data['message']) || empty($data['scheduled_at'])) {
            $this->jsonResponse(['error' => 'Campos obrigatórios ausentes'], 400);
            return;
        }

        $stmt = $this->db->prepare("INSERT INTO campaigns (subject, message, scheduled_at) VALUES (:subject, :message, :scheduled_at)");
        $stmt->execute([
            ':subject' => $data['subject'],
            ':message' => $data['message'],
            ':scheduled_at' => $data['scheduled_at']
        ]);

        $this->jsonResponse(['message' => 'Campanha criada com sucesso', 'id' => $this->db->lastInsertId()], 201);
    }

    /**
     * Atualiza uma campanha
     * @param array $params
     * @return void
     */
    public function update(array $params): void
    {
        $id = $params['id'];
        $data = $this->getInput();

        $fields = [];
        $values = [];

        if (isset($data['subject'])) {
            $fields[] = "subject = ?";
            $values[] = $data['subject'];
        }
        if (isset($data['message'])) {
            $fields[] = "message = ?";
            $values[] = $data['message'];
        }
        if (isset($data['scheduled_at'])) {
            $fields[] = "scheduled_at = ?";
            $values[] = $data['scheduled_at'];
        }
        if (isset($data['status'])) {
            $fields[] = "status = ?";
            $values[] = $data['status'];
        }

        if (empty($fields)) {
            $this->jsonResponse(['message' => 'Nenhuma alteração'], 200);
            return;
        }

        $values[] = $id; // For WHERE clause
        $sql = "UPDATE campaigns SET " . implode(', ', $fields) . " WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        $this->jsonResponse(['message' => 'Campanha atualizada com sucesso']);
    }

    /**
     * Deleta uma campanha
     * @param array $params
     * @return void
     */
    public function delete(array $params): void
    {
        $id = $params['id'];
        $stmt = $this->db->prepare("DELETE FROM campaigns WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $this->jsonResponse(['message' => 'Campanha excluída com sucesso']);
    }
}
