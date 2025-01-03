<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Response\BasketRemovedItemResponseDTO;
use App\DTO\Response\BasketsStatsResponseDTO;
use App\DTO\Response\BasketStatsResponseDTO;
use App\DTO\Response\PriceResponseDTO;
use App\DTO\Response\ProductResponseDTO;
use App\Entity\Basket;
use App\Repository\BasketRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class GetBasketsStatsController
{
    public function __construct(private readonly BasketRepository $basketRepository)
    {
    }

    #[OA\Get(
        path: '/api/baskets-stats',
        summary: 'Get baskets stats',
        tags: ['Basket'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The stats of a basket',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/BasketsStatsResponseDTO'
                )
            ),
        ]
    )]
    #[Route('/api/baskets-stats', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $total = 0;

        $basketResponses = [];
        foreach ($this->basketRepository->findAll() as $basket) {
            $removedItems = [];

            foreach ($basket->getRemovedItems() as $removedItem) {
                $prices = [];

                foreach ($removedItem->getProduct()->getPrices() as $price) {
                    $prices[] = new PriceResponseDTO($price->getType(), $price->getAmount());
                    $total += $price->getAmount();
                }

                $removedItems[] = new BasketRemovedItemResponseDTO(
                    $removedItem->getUuid()->toString(),
                    new ProductResponseDTO(
                        $removedItem->getProduct()->getUuid()->toString(),
                        $removedItem->getProduct()->getTitle(),
                        $prices,
                    ),
                    $removedItem->getRemovedAt()
                );
            }

            $basketResponses[] = new BasketStatsResponseDTO(
                $basket->getUuid()->toString(),
                $basket->getUser()->getUuid()->toString(),
                $removedItems,
                \count($removedItems),
                $total,
            );
        }

        $responseDTO = new BasketsStatsResponseDTO($basketResponses);

        return new JsonResponse($responseDTO, 200);
    }
}
