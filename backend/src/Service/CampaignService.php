<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Repository\CampaignRepository;
use App\Validation\ValidationResult;
use App\Validation\Validator;
use PDOException;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class CampaignService
{
    public function __construct(
        private readonly CampaignRepository $campaigns,
        private readonly Validator $validator,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function list(): array
    {
        return $this->campaigns->findAll();
    }

    /**
     * @return array<string, mixed>
     */
    public function show(int $id): array
    {
        $campaign = $this->campaigns->find($id);
        if ($campaign === null) {
            throw new NotFoundException('Campanha não encontrada');
        }

        return $campaign;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $result = $this->validator->validate($data, [
            'subject' => ['required', 'maxLength255'],
            'message' => 'required',
            'scheduled_at' => ['required', 'scheduledAt'],
        ]);

        if ($result->fails()) {
            throw new ValidationException($result);
        }

        try {
            return $this->campaigns->create(
                (string) $data['subject'],
                (string) $data['message'],
                (string) $data['scheduled_at'],
            );
        } catch (PDOException $e) {
            $this->logger->error('Campaign persist failed', ['exception' => $e]);

            throw new RuntimeException('Erro no banco de dados', 0, $e);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        if (!$this->campaigns->exists($id)) {
            throw new NotFoundException('Campanha não encontrada');
        }

        $fields = $this->buildUpdateFields($data);
        if ($fields === []) {
            return;
        }

        try {
            $this->campaigns->update($id, $fields);
        } catch (PDOException $e) {
            $this->logger->error('Campaign update failed', ['exception' => $e]);

            throw new RuntimeException('Erro no banco de dados', 0, $e);
        }
    }

    public function delete(int $id): void
    {
        if (!$this->campaigns->exists($id)) {
            throw new NotFoundException('Campanha não encontrada');
        }

        try {
            $this->campaigns->delete($id);
        } catch (PDOException $e) {
            $this->logger->error('Campaign delete failed', ['exception' => $e]);

            throw new RuntimeException('Erro no banco de dados', 0, $e);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function buildUpdateFields(array $data): array
    {
        $fields = [];

        if (array_key_exists('subject', $data)) {
            $fields['subject'] = $this->requireNonEmpty('subject', $data['subject']);
        }

        if (array_key_exists('message', $data)) {
            $fields['message'] = $this->requireNonEmpty('message', $data['message']);
        }

        if (array_key_exists('scheduled_at', $data)) {
            $result = $this->validator->validate(
                ['scheduled_at' => $data['scheduled_at']],
                ['scheduled_at' => 'scheduledAt'],
            );
            if ($result->fails()) {
                throw new ValidationException($result);
            }

            $fields['scheduled_at'] = (string) $data['scheduled_at'];
        }

        if (array_key_exists('status', $data)) {
            $result = $this->validator->validate(
                ['status' => $data['status']],
                ['status' => 'campaignStatus'],
            );
            if ($result->fails()) {
                throw new ValidationException($result);
            }

            $fields['status'] = (string) $data['status'];
        }

        return $fields;
    }

    private function requireNonEmpty(string $field, mixed $value): string
    {
        if ($value === '') {
            throw new ValidationException(new ValidationResult([sprintf('%s não pode ser vazio', ucfirst($field))]));
        }

        return (string) $value;
    }
}
