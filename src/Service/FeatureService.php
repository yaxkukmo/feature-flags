<?php
namespace App\Service;

use App\Dto\RuleContext;
use App\Entity\Feature;
use App\Exception\FeatureNotFoundException;
use App\Repository\FeatureRepository;
use App\Service\Strategy\RolloutEvaluationStrategy;
use App\Service\Strategy\RuleEvaluationStrategy;

class FeatureService
{
    public function __construct(
        private FeatureRepository $repository,
    ) { }


    public function isEnabled(string $name, RuleContext $context): bool
    {
        $feature = $this->getFeatureByName($name);
        $isEnabled = $feature->isEnabled();
        if(!$isEnabled) return false;
        $strategy = $feature->getRules()->isEmpty()
            ? new RolloutEvaluationStrategy($context, $feature)
            : new RuleEvaluationStrategy($context, $feature);
        return $strategy->evaluate();
    }

    public function getFeatureByName(string $name): Feature
    {
        $feature = $this->repository->findByNameWithRules($name);
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


