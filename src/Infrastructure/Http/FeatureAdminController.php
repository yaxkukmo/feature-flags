<?php

namespace App\Infrastructure\Http;

use App\Domain\Feature\RuleOperator;
use App\Dto\CreateFeature;
use App\Dto\CreateRule;
use App\Dto\FeatureQuery;
use App\Application\Feature\FeatureAdminServiceInterface;
use App\Dto\UpdateFeature;
use App\Infrastructure\Http\Presenter\FeaturePresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('ROLE_ADMIN')]
final class FeatureAdminController extends AbstractController
{
    public function __construct(
        private FeatureAdminServiceInterface $featureService,
        private ValidatorInterface $validator,
        private FeaturePresenter $presenter
    ) {}

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

    #[Route('/feature/{name}', methods: ['DELETE'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function removeFeature(string $name, Request $request): JsonResponse
    {
        $this->featureService->remove($name);
        return new JsonResponse(['success' => true]);
    }


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


    #[Route('/feature/{name}/rule/{id}', methods: ['DELETE'], requirements: ['name' => '[a-zA-Z0-9_\-]+', 'id' => '[0-9]+'])]
    public function removeRule(int $id, Request $request): JsonResponse
    {
        $this->featureService->removeRule($id);
        return new JsonResponse(['success' => true]);
    }


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
