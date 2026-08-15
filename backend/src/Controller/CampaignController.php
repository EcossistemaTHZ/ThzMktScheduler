<?php

declare(strict_types=1);

namespace App\Controller;

use PDO;

class CampaignController extends BaseController
{
    /**
     * Lista todas as campanhas
     */
    public function index(): void
    {
        $stmt = $this->db->query("SELECT * FROM campaigns ORDER BY scheduled_at ASC");
        $this->jsonResponse($stmt->fetchAll());
    }

    /**
     * Retorna uma campanha pelo id
     * @param array $params
     */
    public function show(array $params): void
    {
        $campaign = $this->findById((int)$params['id']);

        if ($campaign === null) {
            $this->jsonResponse(['error' => 'Campanha não encontrada'], 404);
            return;
        }

        $this->jsonResponse($campaign);
    }

    /**
     * Cria uma campanha
     */
    public function create(): void
    {
        $data = $this->getInput();

        if (empty($data['subject']) || empty($data['message']) || empty($data['scheduled_at'])) {
            $this->jsonResponse(['error' => 'Campos obrigatórios ausentes'], 400);
            return;
        }

        if (strlen($data['subject']) > 255) {
            $this->jsonResponse(['error' => 'Assunto muito longo (máx. 255 caracteres)'], 400);
            return;
        }

        if (!$this->isValidScheduledAt($data['scheduled_at'])) {
            $this->jsonResponse(['error' => 'Data de agendamento inválida ou no passado'], 400);
            return;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO campaigns (subject, message, scheduled_at) VALUES (:subject, :message, :scheduled_at)");
            $stmt->execute([
                ':subject' => $data['subject'],
                ':message' => $data['message'],
                ':scheduled_at' => $data['scheduled_at']
            ]);

            $this->jsonResponse(['message' => 'Campanha criada com sucesso', 'id' => $this->db->lastInsertId()], 201);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            $this->jsonResponse(['error' => 'Erro no banco de dados'], 500);
        }
    }

    /**
     * Atualiza uma campanha
     * @param array $params
     */
    public function update(array $params): void
    {
        $id = (int)$params['id'];
        $data = $this->getInput();

        if ($this->findById($id) === null) {
            $this->jsonResponse(['error' => 'Campanha não encontrada'], 404);
            return;
        }

        $fields = [];
        $values = [];

        if (isset($data['subject'])) {
            if ($data['subject'] === '') {
                $this->jsonResponse(['error' => 'Assunto não pode ser vazio'], 400);
                return;
            }
            $fields[] = "subject = ?";
            $values[] = $data['subject'];
        }
        if (isset($data['message'])) {
            if ($data['message'] === '') {
                $this->jsonResponse(['error' => 'Mensagem não pode ser vazia'], 400);
                return;
            }
            $fields[] = "message = ?";
            $values[] = $data['message'];
        }
        if (isset($data['scheduled_at'])) {
            if (!$this->isValidScheduledAt($data['scheduled_at'])) {
                $this->jsonResponse(['error' => 'Data de agendamento inválida ou no passado'], 400);
                return;
            }
            $fields[] = "scheduled_at = ?";
            $values[] = $data['scheduled_at'];
        }
        if (isset($data['status'])) {
            if (!$this->isValidStatus($data['status'])) {
                $this->jsonResponse(['error' => 'Status inválido'], 400);
                return;
            }
            $fields[] = "status = ?";
            $values[] = $data['status'];
        }

        if (empty($fields)) {
            $this->jsonResponse(['message' => 'Nenhuma alteração'], 200);
            return;
        }

        $values[] = $id;
        $sql = "UPDATE campaigns SET " . implode(', ', $fields) . " WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        $this->jsonResponse(['message' => 'Campanha atualizada com sucesso']);
    }

    /**
     * Deleta uma campanha
     * @param array $params
     */
    public function delete(array $params): void
    {
        $id = (int)$params['id'];

        if ($this->findById($id) === null) {
            $this->jsonResponse(['error' => 'Campanha não encontrada'], 404);
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM campaigns WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $this->jsonResponse(['message' => 'Campanha excluída com sucesso']);
    }

    /**
     * Busca uma campanha pelo id ou retorna null
     * @param int $id
     * @return array|null
     */
    private function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM campaigns WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
}