<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Feature\Feature;
use App\Infrastructure\Persistence\DoctrineFeature;

class FeatureMapper
{
    public function __construct(private RuleMapper $ruleMapper)
    {}

    public function toDomain(DoctrineFeature $feature): Feature
    {
        $rules = array_map(
            fn($rule) => $this->ruleMapper->toDomain($rule),
            $feature->getRules()->toArray()
        );
        return new Feature(
            $feature->getId(),
            $feature->getName(),
            $feature->isEnabled(),
            $rules,
            $feature->getRolloutPercentage()
        );
    }

    public function toPersistence(Feature $feature, ?DoctrineFeature $existing = null): DoctrineFeature
    {
        $doctrineFeature =  $existing ?? new DoctrineFeature();
        $doctrineFeature->setName($feature->name());
        $doctrineFeature->setRolloutPercentage($feature->rolloutPercentage());
        $doctrineFeature->setEnabled($feature->isEnabled());
        return $doctrineFeature;
    }
}
