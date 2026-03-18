<?php
namespace App\Service;

use App\Entity\Feature;
use App\Exception\FeatureNotFoundException;
use App\Repository\FeatureRepository;

class FeatureService
{
    public function __construct(
        private FeatureRepository $repository,
    ) { }


    public function isEnabled(string $name): bool
    {
        $feature = $this->getFeatureByName($name);
        return $feature->isEnabled();
    }

    public function getFeatureByName(string $name): Feature
    {
        $feature = $this->repository->findOneByName($name);
        if (!$feature) {
            throw new FeatureNotFoundException($name);
        }
        return $feature;
    }

    public function toggleFeatureValue(string $name): Feature
    {
        $feature = $this->getFeatureByName($name);
        $feature->setEnabled(!$feature->isEnabled());
        $this->repository->update($feature);
        return $feature;
    }
}


