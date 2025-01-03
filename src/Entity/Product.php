<?php

declare(strict_types=1);

namespace App\Entity;

use App\DTO\ComicDTO;
use App\Entity\Trait\DefaultTrait;
use App\Entity\Trait\SluggableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{
    use DefaultTrait;
    use SluggableTrait;
    use TimestampableTrait;

    #[ORM\Column(type: 'integer')]
    private int $marvelId;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    /**
     * @var Collection<int, Price>
     */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'product', cascade: ['persist', 'remove'])]
    private Collection $prices;

    public function __construct(int $marvelId, string $title, ?string $description, ?string $thumbnail)
    {
        $this->marvelId = $marvelId;
        $this->title = $title;
        $this->description = $description;
        $this->thumbnail = $thumbnail;
        $this->prices = new ArrayCollection();
    }

    public static function createFromDTO(ComicDTO $dto): self
    {
        $product = new self(
            $dto->getMarvelId(),
            $dto->getTitle(),
            $dto->getDescription(),
            $dto->getThumbnail(),
        );

        foreach ($dto->getPrices() as $priceDTO) {
            $product->addPrice(new Price(
                $priceDTO->getType(),
                $priceDTO->getPrice(),
                $product
            ));
        }

        return $product;
    }

    public function getMarvelId(): int
    {
        return $this->marvelId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function addPrice(Price $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
        }
    }

    public function removePrice(Price $price): void
    {
        $this->prices->removeElement($price);
    }

    /**
     * @return Collection<int, Price>
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }
}
