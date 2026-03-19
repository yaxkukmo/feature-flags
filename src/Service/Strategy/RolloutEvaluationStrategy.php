<?php

namespace App\Service\Strategy;

use App\Dto\RuleContext;
use App\Entity\Feature;

class RolloutEvaluationStrategy implements FeatureEvaluationStrategyInterface
{
    public function __construct(private RuleContext $context, private Feature $feature) {}

    public function evaluate(): bool {
        $rolloutPercentage = $this->feature->getRolloutPercentage();
        if(($rolloutPercentage ?? 0) === 0) return false;
        if($rolloutPercentage === 100) return true;
        return ($this->context->userId % 100) < $rolloutPercentage;
    }
}
