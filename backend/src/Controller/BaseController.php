<?php

declare(strict_types=1);

namespace App\Controller;

use App\Config\Database;
use PDO;

class BaseController
{
    protected PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    protected function jsonResponse(mixed $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }

    protected function getInput(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return $input ?? [];
    }
}
