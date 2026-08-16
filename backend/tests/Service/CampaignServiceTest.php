<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Repository\CampaignRepository;
use App\Service\CampaignService;
use App\Tests\TestCase;
use App\Validation\Validator;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

final class CampaignServiceTest extends TestCase
{
    private CampaignService $service;

    protected function setUp(): void
    {
        $logger = new Logger('test', [new StreamHandler('php://stderr')]);
        $this->service = new CampaignService(
            new CampaignRepository($this->createPdo()),
            new Validator(),
            $logger,
        );
    }

    public function testCreateAndList(): void
    {
        $id = $this->service->create([
            'subject' => 'Promo',
            'message' => 'Oferta imperdível',
            'scheduled_at' => '2030-01-01T10:00',
        ]);

        $list = $this->service->list();

        self::assertCount(1, $list);
        self::assertSame($id, $list[0]['id']);
    }

    public function testCreateRejectsMissingFields(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create(['subject' => 'Promo']);
    }

    public function testCreateRejectsPastScheduledAt(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->create([
            'subject' => 'Promo',
            'message' => 'msg',
            'scheduled_at' => date('Y-m-d\TH:i', time() - 3600),
        ]);
    }

    public function testShowThrowsWhenNotFound(): void
    {
        $this->expectException(NotFoundException::class);

        $this->service->show(9999);
    }

    public function testUpdatePartialFields(): void
    {
        $id = $this->service->create([
            'subject' => 'Promo',
            'message' => 'msg',
            'scheduled_at' => '2030-01-01T10:00',
        ]);

        $this->service->update($id, ['status' => 'sent']);

        $campaign = $this->service->show($id);
        self::assertSame('sent', $campaign['status']);
        self::assertSame('Promo', $campaign['subject']);
    }

    public function testDelete(): void
    {
        $id = $this->service->create([
            'subject' => 'Promo',
            'message' => 'msg',
            'scheduled_at' => '2030-01-01T10:00',
        ]);

        $this->service->delete($id);

        $this->expectException(NotFoundException::class);
        $this->service->show($id);
    }
}
