<?php

namespace App\Service\Strategy;

interface FeatureEvaluationStrategyInterface
{
    public function evaluate(): bool;
}
