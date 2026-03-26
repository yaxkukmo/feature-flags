<?php

namespace App\Application\Feature\Strategy;

interface FeatureEvaluationStrategyInterface
{
    public function evaluate(): bool;
}
