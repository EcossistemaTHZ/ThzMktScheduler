<?php

declare(strict_types=1);

namespace App\Exception;

use App\Validation\ValidationResult;

final class ValidationException extends DomainException
{
    public function __construct(public readonly ValidationResult $result)
    {
        parent::__construct($result->firstError());
    }
}
