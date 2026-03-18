<?php
namespace App\Service;

use App\Dto\RuleContext;
use App\Entity\Feature;
use App\Entity\Rule;
use App\Enum\RuleOperator;
use App\Exception\FeatureNotFoundException;
use App\Repository\FeatureRepository;
use Doctrine\Common\Collections\Collection;

class FeatureService
{
    public function __construct(
        private FeatureRepository $repository,
    ) { }


    public function isEnabled(string $name, RuleContext $context): bool
    {
        $feature = $this->getFeatureByName($name);
        $isEnabled = $feature->isEnabled();
        if(!$isEnabled) return false;
        if(!$feature->getRules()->isEmpty()) {
            return $this->areRulesMet($feature->getRules(), $context);
        } else {
            $rolloutPercentage = $feature->getRolloutPercentage();
            if(($rolloutPercentage ?? 0) === 0) return false;
            if($rolloutPercentage === 100) return true;
            var_dump($context->userId % 100);
            return ($context->userId % 100) < $rolloutPercentage;
        }

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

    public function getFeatureByName(string $name): Feature
    {
        $feature = $this->repository->findByNameWithRules($name);
        if (!$feature) {
            throw new FeatureNotFoundException($name);
        }
        return $feature;
    }

    public function toggleFeatureValue(string $name): Feature
    {
        $feature = $this->getFeatureByName($name);
        $feature->setEnabled(!$feature->isEnabled());
        $this->repository->update($feature);
        return $feature;
    }
}


