<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Feature\Rule;
use App\Domain\Feature\RuleOperator;
use Doctrine\ORM\EntityManagerInterface;

class RuleMapper
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}
    public function toDomain(DoctrineRule $rule): Rule
    {
        $operator = $rule->getOperator();
        return new Rule(
            type: $rule->getType(),
            value: $rule->getValue(),
            operator: $operator instanceof RuleOperator ? $operator : RuleOperator::from($operator),
            id: $rule->getId()
        );
    }

    public function toPersistence(Rule $rule, string $name): DoctrineRule
    {
        $doctrineFeature = $this->em->getRepository(DoctrineFeature::class)->findOneBy(['name' => $name]);
        $doctrineRule = new DoctrineRule();
        $doctrineRule->setOperator($rule->operator());
        $doctrineRule->setType($rule->type());
        $doctrineRule->setValue($rule->value());
        $doctrineRule->setFeature($doctrineFeature);
        return $doctrineRule;
    }
}
