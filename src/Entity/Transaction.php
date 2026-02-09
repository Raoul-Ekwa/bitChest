<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Index(columns: ['wallet_id', 'created_at'], name: 'idx_transaction_wallet_date')]
class Transaction
{
    public const TYPE_BUY = 'buy';
    public const TYPE_SELL = 'sell';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wallet $wallet = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cryptocurrency $cryptocurrency = null;

    #[ORM\Column(length: 10)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8)]
    private ?string $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8)]
    private ?string $priceAtTransaction = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $totalAmount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, [self::TYPE_BUY, self::TYPE_SELL])) {
            throw new \InvalidArgumentException('Invalid transaction type');
        }
        $this->type = $type;
        return $this;
    }

    public function isBuy(): bool
    {
        return $this->type === self::TYPE_BUY;
    }

    public function isSell(): bool
    {
        return $this->type === self::TYPE_SELL;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPriceAtTransaction(): ?string
    {
        return $this->priceAtTransaction;
    }

    public function setPriceAtTransaction(string $priceAtTransaction): static
    {
        $this->priceAtTransaction = $priceAtTransaction;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function calculateTotalAmount(): string
    {
        return bcmul($this->quantity, $this->priceAtTransaction, 2);
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
}
