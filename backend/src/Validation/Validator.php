<?php

declare(strict_types=1);

namespace App\Validation;

final class Validator
{
    public const CAMPAIGN_STATUSES = ['pending', 'sent', 'failed'];

    /**
     * @param array<string, mixed> $data
     * @param array<string, string|list<string>> $rules
     */
    public function validate(array $data, array $rules): ValidationResult
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $rulesForField = is_string($fieldRules) ? [$fieldRules] : $fieldRules;

            foreach ($rulesForField as $rule) {
                if ($this->passes($rule, $value)) {
                    continue;
                }

                $errors[] = $this->message($field, $rule);
                break;
            }
        }

        return new ValidationResult($errors);
    }

    public function isValidScheduledAt(string $value): bool
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(?::(\d{2}))?$/', $value, $m)) {
            return false;
        }

        if (!checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return false;
        }

        $parsed = strtotime($value);

        return $parsed !== false && $parsed >= (time() - 300);
    }

    private function passes(string $rule, mixed $value): bool
    {
        return match ($rule) {
            'required' => $value !== null && $value !== '',
            'email' => is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'maxLength255' => is_string($value) && mb_strlen($value) <= 255,
            'campaignStatus' => is_string($value) && in_array($value, self::CAMPAIGN_STATUSES, true),
            'scheduledAt' => is_string($value) && $this->isValidScheduledAt($value),
            default => true,
        };
    }

    private function message(string $field, string $rule): string
    {
        return match ($rule) {
            'required' => sprintf('Campo "%s" é obrigatório', $field),
            'email' => 'E-mail inválido',
            'maxLength255' => 'Assunto muito longo (máx. 255 caracteres)',
            'campaignStatus' => 'Status inválido',
            'scheduledAt' => 'Data de agendamento inválida ou no passado',
            default => 'Valor inválido',
        };
    }
}
