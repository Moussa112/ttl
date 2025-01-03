<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\ProductRequestDTO;
use App\Entity\RemovedItem;
use App\Repository\BasketItemRepository;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class RemoveProductFromBasketController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BasketRepository $basketRepository,
        private readonly ProductRepository $productRepository,
        private readonly BasketItemRepository $basketItemRepository,
    ) {
    }

    #[OA\Post(
        path: '/api/basket/remove',
        summary: 'Remove a product from the shopping basket',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: '#/components/schemas/ProductRequestDTO'
            )
        ),
        tags: ['Basket'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product removed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Removed successfully.'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'errors', type: 'string', example: 'Missing productUuid or userId'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Product not found.'),
                    ]
                )
            ),
        ]
    )]
    #[Route('/api/basket/remove', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] ProductRequestDTO $request): JsonResponse
    {
        $basket = $this->basketRepository->findOneBy(['user' => $request->userId]);

        if (!$basket) {
            return new JsonResponse(
                ['error' => sprintf('Basket for user id: "%s" not found.', $request->userId)],
                404
            );
        }

        $product = $this->productRepository->findOneBy(['uuid' => $request->uuid]);

        if (!$product) {
            return new JsonResponse(
                ['error' => sprintf('Product with uuid: "%s" not found.', $request->uuid)],
                404
            );
        }

        $basketItem = $this->basketItemRepository->findOneBy(['basket' => $basket, 'product' => $product]);

        if (!$basketItem) {
            return new JsonResponse(
                ['error' => sprintf('Basket item not found.')],
                404
            );
        }

        $basket->removeItem($basketItem);

        $removedItem = RemovedItem::create($product, $basket);
        $this->entityManager->persist($removedItem);

        $this->entityManager->remove($basketItem);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Removed successfully.'], 200);
    }
}
