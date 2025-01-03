<?php

declare(strict_types=1);

namespace App\DTO\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'BasketRemovedItemResponseDTO')]
final class BasketRemovedItemResponseDTO
{
    public function __construct(
        #[OA\Property(example: '70f281e5-1d79-4d95-8272-5115fd7d3d99')]
        public readonly string $uuid,

        #[OA\Property(ref: '#/components/schemas/ProductResponseDTO')]
        public readonly ProductResponseDTO $product,

        #[OA\Property(example: '2021-10-10T12:00:00+00:00')]
        public readonly \DateTimeImmutable $removedAt,
    ) {
    }
}
