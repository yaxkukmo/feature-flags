<?php

namespace App\Controller;

use App\Dto\RuleContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FeatureService;
use App\Exception\FeatureNotFoundException;

final class FeatureController extends AbstractController
{
    public function __construct(private FeatureService $featureService) { }

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
