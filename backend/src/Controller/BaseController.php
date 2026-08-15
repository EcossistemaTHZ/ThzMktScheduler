<?php

declare(strict_types=1);

namespace App\Controller;

use App\Config\Database;
use PDO;

class BaseController
{
    protected PDO $db;

    /**
     * Construtor para inicializar a conexão com o banco de dados
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    /**
     * Envia uma resposta JSON
     * @param mixed $data
     * @param int $statusCode
     */
    protected function jsonResponse(mixed $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
    
    /**
     * Recupera os dados enviados no corpo da requisição
     * @return array
     */
    protected function getInput(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return $input ?? [];
    }

    /**
     * Valida um endereço de e-mail
     * @param string $email
     * @return bool
     */
    protected function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida o status de uma campanha
     * @param string $status
     * @return bool
     */
    protected function isValidStatus(string $status): bool
    {
        return in_array($status, ['pending', 'sent', 'failed'], true);
    }

    /**
     * Valida a data de agendamento (formato YYYY-MM-DDTHH:MM[:SS] e data válida)
     * @param string $value
     * @return bool
     */
    protected function isValidScheduledAt(string $value): bool
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(?::(\d{2}))?$/', $value, $m)) {
            return false;
        }

        if (!checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
            return false;
        }

        // Rejeita datas passadas com tolerância de 5 minutos (fuso horário)
        $parsed = strtotime($value);
        return $parsed !== false && $parsed >= (time() - 300);
    }
}
