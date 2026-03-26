<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Feature\Rule;
use App\Infrastructure\Persistence\DoctrineRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Domain\Feature\RuleRepositoryInterface;
use App\Domain\Feature\Exception\RuleNotFoundException;

/**
 * @extends ServiceEntityRepository<Rule>
 */
class DoctrineRuleRepository extends ServiceEntityRepository implements RuleRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private RuleMapper $mapper,
        private FeatureMapper $featureMapper
    )
    {
        parent::__construct($registry, DoctrineRule::class);
    }

    public function  delete(int $id): void
    {
        $affected = $this->getEntityManager()->createQuery(
            'DELETE FROM App\Infrastructure\Persistence\DoctrineRule r WHERE r.id=:id'
        )->setParameter('id', $id)->execute();
        if ($affected === 0) throw new RuleNotFoundException($id);
    }

    public function save(Rule $rule, string $name):void
    {
        $doctrineRule = $this->mapper->toPersistence($rule, $name);
        $this->getEntityManager()->persist($doctrineRule);
        $this->getEntityManager()->flush();
    }
}
