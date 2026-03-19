<?php

namespace App\Domain\Feature;

interface FeatureRepositoryInterface
{
    public function findByNameWithRules(string $name): ?Feature;
    public function save(Feature $feature): void;
}
