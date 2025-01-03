<?php

declare(strict_types=1);

namespace App\DTO\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(title: 'BasketsResponseDTO')]
final class BasketsStatsResponseDTO
{
    /**
     * @param BasketStatsResponseDTO[] $basketsStats
     */
    public function __construct(
        #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/BasketStatsResponseDTO'))]
        public readonly array $basketsStats,
    ) {
    }
}
