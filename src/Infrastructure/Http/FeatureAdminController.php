<?php

namespace App\Infrastructure\Http;

use App\Domain\Feature\RuleOperator;
use App\Dto\CreateFeature;
use App\Dto\CreateRule;
use App\Dto\FeatureQuery;
use App\Application\Feature\FeatureAdminServiceInterface;
use App\Dto\UpdateFeature;
use App\Infrastructure\Http\Presenter\FeaturePresenter;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('ROLE_ADMIN')]
#[OA\Tag(name: 'Admin')]
final class FeatureAdminController extends AbstractController
{
    public function __construct(
        private FeatureAdminServiceInterface $featureService,
        private ValidatorInterface $validator,
        private FeaturePresenter $presenter
    ) {}

    #[OA\Post(
        path: '/feature',
        summary: 'Create a new feature flag',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'checkout_v2'),
                    new OA\Property(property: 'enabled', type: 'boolean', example: false),
                    new OA\Property(property: 'rolloutPercentage', type: 'integer', example: 50),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Feature created'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[Route('/feature', methods: ['POST'])]
    public function addFeature(Request $request): JsonResponse
    {
        $dto = new CreateFeature(
            name: $request->getPayload()->get('name'),
            rolloutPercentage: $request->getPayload()->get('rolloutPercentage'),
            enabled: $request->getPayload()->get('enabled')
        );
        $this->validator->validate($dto);
        $feature = $this->featureService->create($dto);
        return new JsonResponse($this->presenter->present($feature));
    }

    #[OA\Get(
        path: '/feature',
        summary: 'List feature flags',
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer', default: 50)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'sortBy', in: 'query', schema: new OA\Schema(type: 'string', default: 'name')),
            new OA\Parameter(name: 'sortDir', in: 'query', schema: new OA\Schema(type: 'string', default: 'ASC')),
        ],
        responses: [new OA\Response(response: 200, description: 'List of features')]
    )]
    #[Route('/feature', methods: ['GET'])]
    public function listFeatures(Request $request): JsonResponse
    {
        $query = $request->query;
        $featureQuery = new FeatureQuery(
            page: (int)$query->get('page', 1),
            limit: (int)$query->get('limit', 50),
            search: $query->get('search'),
            sortBy: $query->get('sortBy', 'name'),
            sortDir: $query->get('sortDir', 'ASC')
        );
        $results = $this->featureService->list($featureQuery);
        return new JsonResponse([
            'success' => true,
            'data' => $this->presenter->presentList($results)
        ]);
    }

    #[OA\Patch(
        path: '/feature/{name}',
        summary: 'Update a feature flag',
        parameters: [
            new OA\Parameter(name: 'name', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'checkout_v3'),
                    new OA\Property(property: 'enabled', type: 'boolean', example: true),
                    new OA\Property(property: 'rolloutPercentage', type: 'integer', example: 80),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Feature updated'),
            new OA\Response(response: 404, description: 'Feature not found'),
        ]
    )]
    #[Route('/feature/{name}', methods: ['PATCH'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function updateFeature(string $name, Request $request): JsonResponse
    {
        $payload = $request->getPayload();
        $dto = new UpdateFeature(
            name: $payload->get('name'),
            enabled: (bool) $payload->get('enabled'),
            rolloutPercentage: $payload->get('rolloutPercentage'),
        );
        $this->validator->validate($dto);
        $feature = $this->featureService->update($name, $dto);
        return new JsonResponse([
            'success' => true,
            'data' => $this->presenter->present($feature)
        ]);
    }

    #[OA\Delete(
        path: '/feature/{name}',
        summary: 'Delete a feature flag',
        parameters: [
            new OA\Parameter(name: 'name', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Feature deleted'),
            new OA\Response(response: 404, description: 'Feature not found'),
        ]
    )]
    #[Route('/feature/{name}', methods: ['DELETE'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function removeFeature(string $name, Request $request): JsonResponse
    {
        $this->featureService->remove($name);
        return new JsonResponse(['success' => true]);
    }

    #[OA\Post(
        path: '/feature/{name}/rule',
        summary: 'Add a rule to a feature flag',
        parameters: [
            new OA\Parameter(name: 'name', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'type', type: 'string', example: 'country'),
                    new OA\Property(property: 'value', type: 'string', example: 'pl'),
                    new OA\Property(property: 'operator', type: 'string', example: 'equals'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rule added'),
            new OA\Response(response: 404, description: 'Feature not found'),
        ]
    )]
    #[Route('/feature/{name}/rule', methods: ['POST'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function addRule(string $name, Request $request): JsonResponse
    {
        $payload = $request->getPayload();
        $dto = new CreateRule(
            type: $payload->get('type'),
            value: $payload->get('value'),
            operator: RuleOperator::from($payload->get('operator'))
        );
        $this->validator->validate($dto);
        $feature = $this->featureService->addRule($name, $dto);
        return new JsonResponse([
            'success' => true,
            'data' => $this->presenter->present($feature)
        ]);
    }

    #[OA\Delete(
        path: '/feature/{name}/rule/{id}',
        summary: 'Remove a rule from a feature flag',
        parameters: [
            new OA\Parameter(name: 'name', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rule removed'),
            new OA\Response(response: 404, description: 'Rule not found'),
        ]
    )]
    #[Route('/feature/{name}/rule/{id}', methods: ['DELETE'], requirements: ['name' => '[a-zA-Z0-9_\-]+', 'id' => '[0-9]+'])]
    public function removeRule(int $id, Request $request): JsonResponse
    {
        $this->featureService->removeRule($id);
        return new JsonResponse(['success' => true]);
    }

    #[OA\Patch(
        path: '/feature/{name}/toggle',
        summary: 'Toggle a feature flag on/off',
        parameters: [
            new OA\Parameter(name: 'name', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Feature toggled'),
            new OA\Response(response: 404, description: 'Feature not found'),
        ]
    )]
    #[Route('/feature/{name}/toggle', methods: ['PATCH'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function toggle(string $name): JsonResponse
    {
        $feature = $this->featureService->toggleFeature($name);
        return new JsonResponse([
            'success' => true,
            'data' => $this->presenter->present($feature)
        ]);
    }
}
