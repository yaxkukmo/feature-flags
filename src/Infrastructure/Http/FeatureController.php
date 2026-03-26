<?php

namespace App\Infrastructure\Http;

use App\Dto\RuleContext;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Feature\FeatureServiceInterface;
use App\Domain\Feature\Exception\FeatureNotFoundException;

final class FeatureController extends AbstractController
{
    public function __construct(private FeatureServiceInterface $featureService) { }

    #[OA\Get(
        path: '/feature/{name}',
        summary: 'Check if a feature flag is enabled for a user',
        tags: ['Feature'],
        parameters: [
            new OA\Parameter(name: 'name', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'userId', in: 'query', required: true, schema: new OA\Schema(type: 'integer', example: 42)),
            new OA\Parameter(name: 'country', in: 'query', schema: new OA\Schema(type: 'string', example: 'pl')),
            new OA\Parameter(name: 'plan', in: 'query', schema: new OA\Schema(type: 'string', example: 'premium')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Feature status'),
            new OA\Response(response: 400, description: 'Invalid user ID'),
            new OA\Response(response: 404, description: 'Feature not found'),
        ]
    )]
    #[Route('/feature/{name}', methods: ['GET'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function check(string $name, Request $request): JsonResponse
    {
        $userId = $request->query->getInt('userId');
        if ($userId <= 0) {
            return new JsonResponse(['error' => 'Invalid user ID'], 400);
        }

        try {
            $context = new RuleContext(
                $userId,
                $request->query->getString('country'),
                $request->query->getString('plan')
            );
            $isEnabled = $this->featureService->isEnabled($name, $context);
        } catch (FeatureNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }

        return new JsonResponse(['feature' => $name, 'enabled' => $isEnabled]);
    }
}
