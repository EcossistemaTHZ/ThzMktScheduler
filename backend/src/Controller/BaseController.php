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
}
