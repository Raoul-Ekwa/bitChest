<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Client;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private Client $user;

    protected function setUp(): void
    {
        $this->user = new Client();
        $this->user->setFirstName('Jean');
        $this->user->setLastName('Dupont');
        $this->user->setEmail('jean.dupont@email.com');
        $this->user->setPassword('hashed_password');
    }

    public function testGetFullName(): void
    {
        $this->assertSame('Jean Dupont', $this->user->getFullName());
    }

    public function testGetUserIdentifierReturnsEmail(): void
    {
        $this->assertSame('jean.dupont@email.com', $this->user->getUserIdentifier());
    }

    public function testGetRolesAlwaysContainsRoleUser(): void
    {
        $roles = $this->user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testClientHasRoleClient(): void
    {
        $this->assertContains('ROLE_CLIENT', $this->user->getRoles());
    }

    public function testGetRolesReturnsUniqueValues(): void
    {
        $roles = $this->user->getRoles();
        $this->assertSame($roles, array_unique($roles));
    }

    public function testCreatedAtIsSetOnConstruction(): void
    {
        $user = new Client();
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    public function testSetAndGetEmail(): void
    {
        $this->user->setEmail('new@email.com');
        $this->assertSame('new@email.com', $this->user->getEmail());
    }

    public function testSetAndGetPassword(): void
    {
        $this->user->setPassword('new_hash');
        $this->assertSame('new_hash', $this->user->getPassword());
    }

    public function testIsActiveByDefault(): void
    {
        $client = new Client();
        $this->assertTrue($client->isActive());
    }

    public function testSetIsActive(): void
    {
        $this->user->setIsActive(false);
        $this->assertFalse($this->user->isActive());
    }
}
