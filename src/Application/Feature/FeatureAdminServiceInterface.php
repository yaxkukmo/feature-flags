<?php

namespace App\Application\Feature;

use App\Domain\Feature\Feature;
use App\Domain\Feature\Rule;
use App\Dto\CreateFeature;
use App\Dto\FeatureQuery;
use App\Dto\UpdateFeature;
use App\Dto\CreateRule;

interface FeatureAdminServiceInterface
{
    /** @return Feature[] */
    public function list(FeatureQuery $featureQuery): array;
    public function create(CreateFeature $dto): Feature;
    public function remove(string $name): void;
    public function update(string $name, UpdateFeature $dto): Feature;
    public function toggleFeature(string $name): Feature;
    public function addRule(string $name, CreateRule $rule): Feature;
    public function removeRule(int $id): void;
}
