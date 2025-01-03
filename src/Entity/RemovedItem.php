<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DefaultTrait;
use App\Repository\RemovedItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RemovedItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class RemovedItem
{
    use DefaultTrait;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $removedAt = null;

    #[ORM\ManyToOne(inversedBy: 'removedItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Basket $basket = null;

    public function __construct(?Product $product, ?Basket $basket)
    {
        $this->product = $product;
        $this->basket = $basket;
        $this->removedAt = new \DateTimeImmutable();
    }

    public static function create(Product $product, Basket $basket): self
    {
        return new self($product, $basket);
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function getRemovedAt(): ?\DateTimeImmutable
    {
        return $this->removedAt;
    }

    public function setRemovedAt(\DateTimeImmutable $removedAt): static
    {
        $this->removedAt = $removedAt;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): static
    {
        $this->basket = $basket;

        return $this;
    }
}
