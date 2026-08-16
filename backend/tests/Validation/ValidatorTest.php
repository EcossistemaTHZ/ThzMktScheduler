<?php

declare(strict_types=1);

namespace App\Tests\Validation;

use App\Validation\ValidationResult;
use App\Validation\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testEmailRule(): void
    {
        self::assertTrue($this->validator->validate(['email' => 'user@example.com'], ['email' => 'email'])->passes());
        self::assertTrue($this->validator->validate(['email' => 'inválido'], ['email' => 'email'])->fails());
        self::assertTrue($this->validator->validate(['email' => ''], ['email' => 'email'])->fails());
    }

    public function testRequiredRule(): void
    {
        self::assertTrue($this->validator->validate(['name' => 'João'], ['name' => 'required'])->passes());
        self::assertTrue($this->validator->validate(['name' => ''], ['name' => 'required'])->fails());
        self::assertTrue($this->validator->validate([], ['name' => 'required'])->fails());
    }

    public function testMaxLength255Rule(): void
    {
        self::assertTrue($this->validator->validate(['subject' => str_repeat('a', 255)], ['subject' => 'maxLength255'])->passes());
        self::assertTrue($this->validator->validate(['subject' => str_repeat('a', 256)], ['subject' => 'maxLength255'])->fails());
    }

    public function testCampaignStatusRule(): void
    {
        self::assertTrue($this->validator->validate(['status' => 'pending'], ['status' => 'campaignStatus'])->passes());
        self::assertTrue($this->validator->validate(['status' => 'sent'], ['status' => 'campaignStatus'])->passes());
        self::assertTrue($this->validator->validate(['status' => 'failed'], ['status' => 'campaignStatus'])->passes());
        self::assertTrue($this->validator->validate(['status' => 'INVALIDO'], ['status' => 'campaignStatus'])->fails());
    }

    public function testScheduledAtAcceptsFutureTimestamp(): void
    {
        $future = date('Y-m-d\TH:i', time() + 3600);
        self::assertTrue($this->validator->validate(['scheduled_at' => $future], ['scheduled_at' => 'scheduledAt'])->passes());
    }

    public function testScheduledAtRejectsPastTimestamp(): void
    {
        $past = date('Y-m-d\TH:i', time() - 86400);
        self::assertTrue($this->validator->validate(['scheduled_at' => $past], ['scheduled_at' => 'scheduledAt'])->fails());
    }

    public function testScheduledAtRejectsInvalidFormat(): void
    {
        self::assertTrue($this->validator->validate(['scheduled_at' => '10/10/2030'], ['scheduled_at' => 'scheduledAt'])->fails());
        self::assertTrue($this->validator->validate(['scheduled_at' => '2030-99-99T10:00'], ['scheduled_at' => 'scheduledAt'])->fails());
    }

    public function testReturnsErrorMessages(): void
    {
        $result = $this->validator->validate([], ['name' => 'required']);

        self::assertInstanceOf(ValidationResult::class, $result);
        self::assertTrue($result->fails());
        self::assertSame('Campo "name" é obrigatório', $result->firstError());
    }
}
