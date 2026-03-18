<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\FeatureRepository;

final class FeatureController extends AbstractController
{
    #[Route('/feature', name: 'app_feature')]
    public function index(): Response
    {
        return $this->render('feature/index.html.twig', [
            'controller_name' => 'FeatureController',
        ]);
    }

    #[Route('/feature/{name}', methods: ['GET'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function check(string $name, FeatureRepository $repository): JsonResponse
    {
        $feature = $repository->findOneBy(['name' => $name]);
        if (!$feature) {
            return new JsonResponse(['error' => 'Feature not found'], 404);
        }
        return new JsonResponse(['feature' => $name, 'enabled' => $feature->isEnabled()]);
    }

    #[Route('feature/{name}/toggle', methods: ['GET'], requirements: ['name' => '[a-zA-Z0-9_\-]+'])]
    public function toggle(string $name, FeatureRepository $repository): JsonResponse
    {
        $feature = $repository->findOneBy(['name' => $name]);
        if (!$feature) {
            return new JsonResponse(['error' => 'Feature not found'], 404);
        }
        $feature->setEnabled(!$feature->isEnabled());
        $repository->update($feature);
        return new JsonResponse(['feature' => $name, 'enabled' => $feature->isEnabled()]);
    }
}
