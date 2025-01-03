<?php

declare(strict_types=1);

namespace App\DTO;

readonly class ComicDTO
{
    /** @param array<ComicPriceDTO> $prices */
    private function __construct(
        private int $marvelId,
        private string $title,
        private ?string $description,
        private string $thumbnail,
        private array $prices,
    ) {
    }

    /** @param array<string, mixed> $data */
    public static function createFromApi(array $data): self
    {
        $prices = array_map(
            fn (array $prices) => ComicPriceDTO::createFromApi($prices),
            $data['prices']
        );

        return new self(
            $data['id'],
            $data['title'],
            $data['description'] ?? null,
            $data['thumbnail']['path'].'.'.$data['thumbnail']['extension'],
            $prices,
        );
    }

    public function getMarvelId(): int
    {
        return $this->marvelId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * @return array<int, ComicPriceDTO>
     */
    public function getPrices(): array
    {
        return $this->prices;
    }
}
