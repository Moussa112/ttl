<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DefaultTrait;
use App\Repository\BasketItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class BasketItem
{
    use DefaultTrait;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Basket $basket = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    public function __construct(?Basket $basket, Product $product, ?\DateTimeImmutable $addedAt)
    {
        $this->basket = $basket;
        $this->product = $product;
        $this->addedAt = $addedAt;
    }

    public static function create(Product $product, Basket $basket): self
    {
        return new self($basket, $product, new \DateTimeImmutable());
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): void
    {
        $this->basket = $basket;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(?\DateTimeImmutable $addedAt): void
    {
        $this->addedAt = $addedAt;
    }
}
