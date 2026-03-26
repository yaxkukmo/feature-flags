<?php

namespace App\Application\Feature;

use App\Domain\Feature\Exception\FeatureNotFoundException;
use App\Domain\Feature\Feature;
use App\Domain\Feature\FeatureRepositoryInterface;
use App\Domain\Feature\Rule;
use App\Domain\Feature\RuleRepositoryInterface;
use App\Dto\CreateFeature;
use App\Dto\CreateRule;
use App\Dto\FeatureQuery;
use App\Dto\UpdateFeature;


class FeatureAdminService implements FeatureAdminServiceInterface
{
    public function __construct(
        private FeatureRepositoryInterface $repository,
        private RuleRepositoryInterface $ruleRepository
    ) {}

    /** @return Feature[] */
    public function list(FeatureQuery $featureQuery): array
    {
        return $this->repository->findPaginated($featureQuery);
    }

    public function create(CreateFeature $createFeature): Feature
    {
        $feature = new Feature(
            id: null,
            name: $createFeature->name,
            enabled: $createFeature->enabled,
            rules: [],
            rolloutPercentage: $createFeature->rolloutPercentage
        );
        return $this->repository->save($createFeature->name, $feature);
    }

    public function remove(string $name): void
    {
        $this->repository->delete($name);
    }

    public function update(string $name, UpdateFeature $updateFeature): Feature
    {
        $feature = new Feature(
            id: null,
            name: $updateFeature->name,
            enabled: $updateFeature->enabled,
            rules: [],
            rolloutPercentage: $updateFeature->rolloutPercentage
        );
        return $this->repository->save($name, $feature);
    }

    public function addRule(string $name, CreateRule $createRule): Feature
    {
        $rule = new Rule(
            type: $createRule->type,
            value: $createRule->value,
            operator: $createRule->operator,
            id: null
        );
        $this->ruleRepository->save($rule, $name);
        $feature = $this->repository->findByNameWithRules($name);
        if (!$feature) throw new FeatureNotFoundException($name);
            return $feature;
    }

    public function removeRule(int $id): void
    {
        $this->ruleRepository->delete($id);
    }


    public function toggleFeature(string $name): Feature
    {
        return $this->repository->toggle($name);
    }
}
