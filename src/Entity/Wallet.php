<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'wallet', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $balance = '500.00';

    /**
     * @var Collection<int, Holding>
     */
    #[ORM\OneToMany(targetEntity: Holding::class, mappedBy: 'wallet', orphanRemoval: true)]
    private Collection $holdings;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'wallet', orphanRemoval: true)]
    private Collection $transactions;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->holdings = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getBalance(): string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;
        return $this;
    }

    public function addToBalance(string $amount): static
    {
        $this->balance = bcadd($this->balance, $amount, 2);
        return $this;
    }

    public function subtractFromBalance(string $amount): static
    {
        $this->balance = bcsub($this->balance, $amount, 2);
        return $this;
    }

    /**
     * @return Collection<int, Holding>
     */
    public function getHoldings(): Collection
    {
        return $this->holdings;
    }

    public function addHolding(Holding $holding): static
    {
        if (!$this->holdings->contains($holding)) {
            $this->holdings->add($holding);
            $holding->setWallet($this);
        }
        return $this;
    }

    public function removeHolding(Holding $holding): static
    {
        if ($this->holdings->removeElement($holding)) {
            if ($holding->getWallet() === $this) {
                $holding->setWallet(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setWallet($this);
        }
        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            if ($transaction->getWallet() === $this) {
                $transaction->setWallet(null);
            }
        }
        return $this;
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
