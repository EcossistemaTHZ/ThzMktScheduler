<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ConflictException;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Repository\UserRepository;
use App\Validation\Validator;
use PDOException;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class UserService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly Validator $validator,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(): array
    {
        return $this->users->findAll();
    }

    /**
     * @return array<string, mixed>
     */
    public function show(int $id): array
    {
        $user = $this->users->find($id);
        if ($user === null) {
            throw new NotFoundException('Usuário não encontrado');
        }

        return $user;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $this->assertValid($data);

        try {
            return $this->users->create((string) $data['name'], (string) $data['email']);
        } catch (PDOException $e) {
            throw $this->translatePersistError($e);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        if (!$this->users->exists($id)) {
            throw new NotFoundException('Usuário não encontrado');
        }

        $this->assertValid($data);

        try {
            $this->users->update($id, (string) $data['name'], (string) $data['email']);
        } catch (PDOException $e) {
            throw $this->translatePersistError($e);
        }
    }

    public function delete(int $id): void
    {
        if (!$this->users->exists($id)) {
            throw new NotFoundException('Usuário não encontrado');
        }

        $this->users->delete($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assertValid(array $data): void
    {
        $result = $this->validator->validate($data, [
            'name' => 'required',
            'email' => ['required', 'email'],
        ]);

        if ($result->fails()) {
            throw new ValidationException($result);
        }
    }

    private function translatePersistError(PDOException $e): RuntimeException
    {
        if (str_contains($e->getMessage(), 'UNIQUE')) {
            return new ConflictException('E-mail já está cadastrado');
        }

        $this->logger->error('User persist failed', ['exception' => $e]);

        return new RuntimeException('Erro no banco de dados', 0, $e);
    }
}
