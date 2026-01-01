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
    /**
     * Atualiza um usuário
     * @param array $params
     * @return void
     */
    public function update(array $params): void
    {
        $id = $params['id'];
        $data = $this->getInput();

        if (empty($data['name']) || empty($data['email'])) {
            $this->jsonResponse(['error' => 'Nome e E-mail são obrigatórios'], 400);
            return;
        }

        try {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
            $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':id' => $id
            ]);
            $this->jsonResponse(['message' => 'Usuário atualizado com sucesso']);
        } catch (\PDOException $e) {
            $this->jsonResponse(['error' => 'Erro ao atualizar usuário'], 500);
        }
    }

    /**
     * Deleta um usuário
     * @param array $params
     * @return void
     */
    public function delete(array $params): void
    {
        $id = $params['id'];
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $this->jsonResponse(['message' => 'Usuário excluído com sucesso']);
        } catch (\PDOException $e) {
            $this->jsonResponse(['error' => 'Erro ao excluir usuário'], 500);
        }
    }
}
