<?php

namespace App\Application\Feature\Strategy;

use App\Dto\RuleContext;
use App\Domain\Feature\Feature;
use App\Domain\Feature\Rule;
use App\Domain\Feature\RuleOperator;

class RuleEvaluationStrategy implements FeatureEvaluationStrategyInterface
{
    public function __construct(private RuleContext $context, private Feature $feature) {}

    public function evaluate(): bool {
        return $this->areRulesMet($this->feature->rules(), $this->context);
    }

    private function areRulesMet(array $rules, RuleContext $context): bool
    {
        foreach($rules as $rule) {
            $result = match($rule->type()) {
                'country' => $this->isRuleMet($rule, $context->country),
                'user_id' => $this->isRuleMet($rule, (int)$context->userId),
                'plan' => $this->isRuleMet($rule, $context->plan),
            };
            if (!$result) return false;
        };
        return true;
    }

    private function isRuleMet(Rule $rule, mixed $contextValue): bool
    {
        return match($rule->operator()) {
            RuleOperator::EQUALS => $rule->value() == $contextValue,
            RuleOperator::LESS_THAN => $contextValue < (int) $rule->value(),
            RuleOperator::GREATER_THAN => $contextValue > (int) $rule->value(),
            RuleOperator::IN => in_array($contextValue, $rule->value())

        };
    }
}
