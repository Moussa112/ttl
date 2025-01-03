<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\MarvelApiClientInterface;
use App\DTO\ComicDTO;
use Psr\Log\LoggerInterface;

readonly class MarvelService
{
    public function __construct(private MarvelApiClientInterface $marvelApiClient, private LoggerInterface $logger)
    {
    }

    /** @return ComicDTO[] */
    public function fetchComics(int $limit = 1, int $offset = 0): array
    {
        try {
            $comics = $this->marvelApiClient->fetchComics($limit, $offset);
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch comics from Marvel API.', ['exception' => $e]);

            return [];
        }

        return array_map(
            fn ($comicData) => ComicDTO::createFromApi($comicData),
            $comics
        );
    }
}
