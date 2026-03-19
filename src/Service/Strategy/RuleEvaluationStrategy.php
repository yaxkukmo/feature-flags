<?php

namespace App\Service\Strategy;

use App\Dto\RuleContext;
use App\Entity\Feature;
use App\Entity\Rule;
use App\Enum\RuleOperator;
use Doctrine\Common\Collections\Collection;

class RuleEvaluationStrategy implements FeatureEvaluationStrategyInterface
{
    public function __construct(private RuleContext $context, private Feature $feature) {}

    public function evaluate(): bool {
        return $this->areRulesMet($this->feature->getRules(), $this->context);
    }

    private function areRulesMet(Collection $rules, RuleContext $context): bool
    {
        foreach($rules as $rule) {
            $result = match($rule->getType()) {
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
        return match($rule->getOperator()) {
            RuleOperator::EQUALS => $rule->getValue() == $contextValue,
            RuleOperator::LESS_THAN => $contextValue < (int) $rule->getValue(),
            RuleOperator::GREATER_THAN => $contextValue > (int) $rule->getValue(),
            RuleOperator::IN => in_array($contextValue, $rule->getValue())

        };
    }
}
