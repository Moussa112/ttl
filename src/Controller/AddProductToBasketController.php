<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Request\ProductRequestDTO;
use App\Entity\Basket;
use App\Entity\BasketItem;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class AddProductToBasketController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BasketRepository $basketRepository,
        private readonly ProductRepository $productRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[OA\Post(
        path: '/api/basket/add',
        summary: 'Add a product to the shopping basket',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: '#/components/schemas/ProductRequestDTO'
            )
        ),
        tags: ['Basket'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product added successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Added successfully.'),
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
    #[Route('/api/basket/add', methods: ['POST'])]
    public function __invoke(#[MapRequestPayload] ProductRequestDTO $request): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['uuid' => $request->uuid]);

        if (!$product) {
            return new JsonResponse(
                ['error' => sprintf('Product with uuid: "%s" not found.', $request->uuid)],
                404
            );
        }

        $user = $this->userRepository->find($request->userId);

        if (!$user) {
            return new JsonResponse(
                ['error' => sprintf('User with id: "%s" not found.', $request->userId)],
                404
            );
        }

        $basket = $this->basketRepository->findOneBy(['user' => $request->userId]);

        if (!$basket) {
            $basket = new Basket($user);
        }

        $item = BasketItem::create($product, $basket);

        $this->entityManager->persist($item);

        $basket->addItem($item);

        $this->entityManager->persist($basket);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Added successfully.'], 200);
    }
}
