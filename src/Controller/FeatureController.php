<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FeatureService;
use App\Exception\FeatureNotFoundException;

final class FeatureController extends AbstractController
{
    public function __construct(private FeatureService $featureService) { }

    #[Route('/feature/{name}', methods: ['GET'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function check(string $name): JsonResponse
    {
        try {
            $feature = $this->featureService->getFeatureByName($name);
        } catch (FeatureNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }

        return new JsonResponse(['feature' => $name, 'enabled' => $feature->isEnabled()]);
    }

    #[Route('/feature/{name}/toggle', methods: ['GET'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function toggle(string $name): JsonResponse
    {
        try {
            $feature = $this->featureService->toggleFeatureValue($name);
        } catch (FeatureNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }

        return new JsonResponse(['feature' => $name, 'enabled' => $feature->isEnabled()]);
    }
}
