<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'ComicPriceDTO')]
readonly class ComicPriceDTO
{
    private function __construct(
        #[OA\Property(example: 'digitalPrice')]
        private string $type,

        #[OA\Property(example: '20.50')]
        private ?float $price,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function createFromApi(array $data): self
    {
        return new self(
            $data['type'],
            $data['price'],
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }
}
