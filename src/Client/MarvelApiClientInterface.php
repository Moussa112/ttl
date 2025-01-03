<?php

declare(strict_types=1);

namespace App\Client;

interface MarvelApiClientInterface
{
    /** @return array<int, mixed> */
    public function fetchComics(int $limit = 1, int $offset = 0): array;
}
