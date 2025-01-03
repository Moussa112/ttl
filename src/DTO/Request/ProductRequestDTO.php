<?php

declare(strict_types=1);

namespace App\DTO\Request;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(title: 'ProductRequestDTO')]
final class ProductRequestDTO
{
    public function __construct(
        #[OA\Property(example: '70f281e5-1d79-4d95-8272-5115fd7d3d99')]
        #[Assert\NotBlank(message: 'productUuid is required')]
        #[Assert\Uuid(message: 'productUuid must be a valid UUID.')]
        public readonly string $uuid,

        #[OA\Property(example: 1)]
        #[Assert\NotBlank(message: 'userId is required')]
        public readonly int $userId,
    ) {
    }
}
