<?php

declare(strict_types=1);

namespace App\DTO\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'PriceResponseDTO')]
final class PriceResponseDTO
{
    public function __construct(
        #[OA\Property(example: 'digitalPrice')]
        public readonly string $type,

        #[OA\Property(example: '20.50')]
        public readonly float $amount,
    ) {
    }
}