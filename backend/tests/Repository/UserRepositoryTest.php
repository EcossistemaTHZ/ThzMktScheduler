<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use App\Tests\TestCase;
use PDOException;

final class UserRepositoryTest extends TestCase
{
    private UserRepository $users;

    protected function setUp(): void
    {
        $this->users = new UserRepository($this->createPdo());
    }

    public function testCreateAndFind(): void
    {
        $id = $this->users->create('João', 'joao@example.com');

        $user = $this->users->find($id);

        self::assertNotNull($user);
        self::assertSame('João', $user['name']);
        self::assertSame('joao@example.com', $user['email']);
    }

    public function testFindAllOrderedByName(): void
    {
        $this->users->create('Bia', 'bia@example.com');
        $this->users->create('Ana', 'ana@example.com');

        $all = $this->users->findAll();

        self::assertCount(2, $all);
        self::assertSame('Ana', $all[0]['name']);
    }

    public function testExists(): void
    {
        $id = $this->users->create('João', 'joao@example.com');

        self::assertTrue($this->users->exists($id));
        self::assertFalse($this->users->exists(9999));
    }

    public function testUpdate(): void
    {
        $id = $this->users->create('João', 'joao@example.com');

        $this->users->update($id, 'João Silva', 'joao.silva@example.com');

        $user = $this->users->find($id);
        self::assertSame('João Silva', $user['name']);
        self::assertSame('joao.silva@example.com', $user['email']);
    }

    public function testDelete(): void
    {
        $id = $this->users->create('João', 'joao@example.com');

        $this->users->delete($id);

        self::assertNull($this->users->find($id));
    }

    public function testDuplicateEmailThrows(): void
    {
        $this->users->create('João', 'joao@example.com');

        $this->expectException(PDOException::class);
        $this->users->create('Outra', 'joao@example.com');
    }
}
