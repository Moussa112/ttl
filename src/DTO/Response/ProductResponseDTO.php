<?php

declare(strict_types=1);

namespace App\DTO\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'ProductResponseDTO')]
final class ProductResponseDTO
{
    /**
     * @param array<PriceResponseDTO> $prices
     */
    public function __construct(
        #[OA\Property(example: '70f281e5-1d79-4d95-8272-5115fd7d3d99')]
        public readonly string $uuid,

        #[OA\Property(example: 'An awesome comic')]
        public readonly string $title,

        #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/PriceResponseDTO'))]
        public readonly array $prices,
    ) {
    }
}
