<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\MarvelApiClientInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class MarvelApiClient implements MarvelApiClientInterface
{
    public function __construct(
        private HttpClientInterface $marvelApiClient,
        private string $publicKey,
        private string $privateKey,
    ) {
    }

    /** @return array<string, mixed> */
    private function generateAuthParams(): array
    {
        $timestamp = time();

        return [
            'ts' => $timestamp,
            'apikey' => $this->publicKey,
            'hash' => md5($timestamp.$this->privateKey.$this->publicKey),
        ];
    }

    /** @return array<int, mixed> */
    public function fetchComics(int $limit = 1, int $offset = 0): array
    {
        $authParams = $this->generateAuthParams();

        $response = $this->marvelApiClient->request('GET', 'comics', [
            'query' => array_merge($authParams, [
                'limit' => $limit,
                'offset' => $offset,
            ]),
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Failed to fetch comics from Marvel API.');
        }

        return $response->toArray()['data']['results'];
    }
}
