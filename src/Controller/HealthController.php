<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Info(version: '1.0', title: 'An awesome api!')]
#[AsController]
class HealthController
{
    #[OA\Get(
        path: '/api/health',
        description: 'This endpoint is used to verify the health and status of the API.',
        summary: 'Check the health of the API',
        responses: [
            new OA\Response(
                response: 200,
                description: 'API is healthy',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                    ]
                )
            ),
        ]
    )]
    #[Route('/api/health', name: 'health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok'], 200);
    }
}
