<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Message\Query\GetBasketQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
final class GetBasketController
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    #[OA\Get(
        path: '/api/user/{user}/basket',
        summary: 'Get a basket with products by user',
        tags: ['Basket'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'A basket with the products',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/BasketResponseDTO'
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User has no basket yet.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'errors', type: 'string', example: 'User has no basket yet.'),
                    ]
                )
            ),
        ]
    )]
    #[Route('/api/user/{user}/basket/', methods: ['GET'])]
    public function __invoke(User $user, SerializerInterface $serializer): JsonResponse
    {
        try {
            $envelope = $this->queryBus->dispatch(new GetBasketQuery($user->getUuid()));

            $handledStamp = $envelope->last(HandledStamp::class);

            if (!$handledStamp) {
                throw new \RuntimeException('No handler returned a result.');
            }

            $response = $handledStamp->getResult();
        } catch (\Throwable $exception) {
            return new JsonResponse(['errors' => $exception->getMessage()], 404);
        }

        return new JsonResponse($serializer->serialize($response, 'json'), 200, [], true);
    }
}
