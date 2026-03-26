<?php

namespace App\Domain\Feature;

use App\Dto\FeatureQuery;

interface FeatureRepositoryInterface
{
    public function findByNameWithRules(string $name): ?Feature;
    public function save(string $name, Feature $feature): Feature;
    /* @return Feature[] */
    public function findPaginated(FeatureQuery $featureQuery): array;
    public function delete(string $name): void;
    public function toggle(string $name): Feature;
}
