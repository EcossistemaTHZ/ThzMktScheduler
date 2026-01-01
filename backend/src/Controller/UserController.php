<?php

declare(strict_types=1);

namespace App\Controller;

class UserController extends BaseController
{
    /**
     * Lista todos os usuários
     * @return void
     */
    public function index(): void
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY name ASC");
        $users = $stmt->fetchAll();
        $this->jsonResponse($users);
    }

    /**
     * Cria um usuário
     * @return void
     */
    public function create(): void
    {
        $data = $this->getInput();

        if (empty($data['name']) || empty($data['email'])) {
            $this->jsonResponse(['error' => 'Nome e E-mail são obrigatórios'], 400);
            return;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
            $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email']
            ]);
            $this->jsonResponse(['message' => 'Usuário criado com sucesso', 'id' => $this->db->lastInsertId()], 201);
        } catch (\PDOException $e) {
            if (str_contains($e->getMessage(), 'UNIQUE')) {
                $this->jsonResponse(['error' => 'E-mail já está cadastrado'], 409);
            } else {
                $this->jsonResponse(['error' => 'Erro no banco de dados'], 500);
            }
        }
    }
}
