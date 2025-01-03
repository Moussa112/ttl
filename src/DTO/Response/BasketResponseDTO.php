<?php

declare(strict_types=1);

namespace App\DTO\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'BasketResponseDTO')]
final class BasketResponseDTO
{
    /**
     * @param BasketItemResponseDTO[] $items
     */
    public function __construct(
        #[OA\Property(example: '70f281e5-1d79-4d95-8272-5115fd7d3d99')]
        public readonly string $uuid,

        #[OA\Property(example: '70f281e5-1d79-4d95-8272-5115fd7d3d99')]
        public readonly string $userId,

        #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/BasketItemResponseDTO'))]
        public readonly array $items,

        #[OA\Property(example: 1)]
        public readonly int $count,

        #[OA\Property(example: 20.50)]
        public readonly float $total,
    ) {
    }
}
