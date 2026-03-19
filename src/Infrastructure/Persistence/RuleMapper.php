<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Feature\Rule;



class RuleMapper
{
    public function toDomain(DoctrineRule $rule): Rule
    {
        return new Rule(
            $rule->getId(),
            $rule->getType(),
            $rule->getValue(),
            $rule->getOperator()
        );
    }
}
