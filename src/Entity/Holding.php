<?php

namespace App\Entity;

use App\Repository\HoldingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoldingRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_wallet_crypto', columns: ['wallet_id', 'cryptocurrency_id'])]
class Holding
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'holdings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wallet $wallet = null;

    #[ORM\ManyToOne(inversedBy: 'holdings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cryptocurrency $cryptocurrency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8)]
    private string $quantity = '0.00000000';

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8)]
    private string $averagePurchasePrice = '0.00000000';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): static
    {
        $this->wallet = $wallet;
        return $this;
    }

    public function getCryptocurrency(): ?Cryptocurrency
    {
        return $this->cryptocurrency;
    }

    public function setCryptocurrency(?Cryptocurrency $cryptocurrency): static
    {
        $this->cryptocurrency = $cryptocurrency;
        return $this;
    }

    public function getQuantity(): string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function addQuantity(string $amount): static
    {
        $this->quantity = bcadd($this->quantity, $amount, 8);
        return $this;
    }

    public function subtractQuantity(string $amount): static
    {
        $this->quantity = bcsub($this->quantity, $amount, 8);
        return $this;
    }

    public function getAveragePurchasePrice(): string
    {
        return $this->averagePurchasePrice;
    }

    public function setAveragePurchasePrice(string $averagePurchasePrice): static
    {
        $this->averagePurchasePrice = $averagePurchasePrice;
        return $this;
    }

    public function getTotalValue(): string
    {
        $currentPrice = $this->cryptocurrency?->getCurrentPrice() ?? '0';
        return bcmul($this->quantity, $currentPrice, 2);
    }

    public function getTotalCost(): string
    {
        return bcmul($this->quantity, $this->averagePurchasePrice, 2);
    }

    public function getProfitLoss(): string
    {
        return bcsub($this->getTotalValue(), $this->getTotalCost(), 2);
    }

    public function getProfitLossPercentage(): string
    {
        $cost = $this->getTotalCost();
        if (bccomp($cost, '0', 2) === 0) {
            return '0.00';
        }
        return bcmul(bcdiv($this->getProfitLoss(), $cost, 4), '100', 2);
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
