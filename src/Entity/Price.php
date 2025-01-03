<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\DefaultTrait;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Price
{
    use DefaultTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist'], inversedBy: 'prices')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(length: 255)]
    private string $type;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $amount = null;

    public function __construct(string $type, ?float $amount, Product $product)
    {
        $this->type = $type;
        $this->amount = $amount;
        $this->product = $product;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
