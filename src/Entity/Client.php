<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    #[ORM\OneToOne(mappedBy: 'client', cascade: ['persist', 'remove'])]
    private ?Wallet $wallet = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column]
    private bool $isActive = true;

    public function __construct()
    {
        parent::__construct();
        $this->setRoles(['ROLE_CLIENT']);
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet): static
    {
        if ($wallet->getClient() !== $this) {
            $wallet->setClient($this);
        }

        $this->wallet = $wallet;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }
}
