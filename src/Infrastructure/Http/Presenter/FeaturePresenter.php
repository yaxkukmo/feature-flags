<?php

namespace App\Infrastructure\Http\Presenter;

use App\Domain\Feature\Feature;
use App\Domain\Feature\Rule;

class FeaturePresenter
{
    public function presentList(array $results): array
    {
        return array_map(fn(Feature $feature) => $this->present($feature), $results);
    }

    public function present(Feature $feature): array
    {
        return [
            'id' => $feature->id(),
            'name' => $feature->name(),
            'enabled' => $feature->isEnabled(),
            'rolloutPercentage' => $feature->rolloutPercentage(),
            'rules' => array_map(fn(Rule $rule) => [
                'id' => $rule->id(),
                'type' => $rule->type(),
                'value' => $rule->value(),
                'operator' => $rule->operator()
            ], $feature->rules()),
        ];
    }
}
