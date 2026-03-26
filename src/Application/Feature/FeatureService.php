<?php

namespace App\Application\Feature;

use App\Dto\RuleContext;
use App\Domain\Feature\Feature;
use App\Domain\Feature\Exception\FeatureNotFoundException;
use App\Domain\Feature\FeatureRepositoryInterface;
use App\Application\Feature\Strategy\RolloutEvaluationStrategy;
use App\Application\Feature\Strategy\RuleEvaluationStrategy;

class FeatureService implements FeatureServiceInterface
{
    public function __construct(
        private FeatureRepositoryInterface $repository,
    ) { }


    public function isEnabled(string $name, RuleContext $context): bool
    {
        $feature = $this->repository->findByNameWithRules($name);
        if (!$feature) {
            throw new FeatureNotFoundException($name);
        }
        $isEnabled = $feature->isEnabled();
        if(!$isEnabled) return false;
        $strategy = empty($feature->rules())
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
}


