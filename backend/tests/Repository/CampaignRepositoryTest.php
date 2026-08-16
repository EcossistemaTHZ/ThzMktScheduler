<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\CampaignRepository;
use App\Tests\TestCase;

final class CampaignRepositoryTest extends TestCase
{
    private CampaignRepository $campaigns;

    protected function setUp(): void
    {
        $this->campaigns = new CampaignRepository($this->createPdo());
    }

    public function testCreateAndFind(): void
    {
        $id = $this->campaigns->create('Promo', 'Mensagem', '2030-01-01T10:00');

        $campaign = $this->campaigns->find($id);

        self::assertNotNull($campaign);
        self::assertSame('Promo', $campaign['subject']);
        self::assertSame('pending', $campaign['status']);
    }

    public function testFindAllOrderedByScheduledAt(): void
    {
        $this->campaigns->create('B', 'msg', '2030-05-01T10:00');
        $this->campaigns->create('A', 'msg', '2030-01-01T10:00');

        $all = $this->campaigns->findAll();

        self::assertCount(2, $all);
        self::assertSame('A', $all[0]['subject']);
    }

    public function testUpdatePartialFields(): void
    {
        $id = $this->campaigns->create('Promo', 'Mensagem', '2030-01-01T10:00');

        $this->campaigns->update($id, ['status' => 'sent']);

        $campaign = $this->campaigns->find($id);
        self::assertSame('sent', $campaign['status']);
        self::assertSame('Promo', $campaign['subject']);
    }

    public function testExists(): void
    {
        $id = $this->campaigns->create('Promo', 'Mensagem', '2030-01-01T10:00');

        self::assertTrue($this->campaigns->exists($id));
        self::assertFalse($this->campaigns->exists(9999));
    }

    public function testDelete(): void
    {
        $id = $this->campaigns->create('Promo', 'Mensagem', '2030-01-01T10:00');

        $this->campaigns->delete($id);

        self::assertNull($this->campaigns->find($id));
    }
}
