<?php

namespace App\Entity;

use App\Repository\CryptocurrencyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryptocurrencyRepository::class)]
class Cryptocurrency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $symbol = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 18, scale: 8)]
    private string $currentPrice = '0.00000000';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, Quote>
     */
    #[ORM\OneToMany(targetEntity: Quote::class, mappedBy: 'cryptocurrency', orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $quotes;

    /**
     * @var Collection<int, Holding>
     */
    #[ORM\OneToMany(targetEntity: Holding::class, mappedBy: 'cryptocurrency')]
    private Collection $holdings;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'cryptocurrency')]
    private Collection $transactions;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->quotes = new ArrayCollection();
        $this->holdings = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = strtoupper($symbol);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getCurrentPrice(): string
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(string $currentPrice): static
    {
        $this->currentPrice = $currentPrice;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Collection<int, Quote>
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): static
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes->add($quote);
            $quote->setCryptocurrency($this);
        }
        return $this;
    }

    public function removeQuote(Quote $quote): static
    {
        if ($this->quotes->removeElement($quote)) {
            if ($quote->getCryptocurrency() === $this) {
                $quote->setCryptocurrency(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Holding>
     */
    public function getHoldings(): Collection
    {
        return $this->holdings;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
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

    public function getLatestQuote(): ?Quote
    {
        return $this->quotes->first() ?: null;
    }
}
