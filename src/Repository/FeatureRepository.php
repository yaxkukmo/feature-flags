<?php

namespace App\Repository;

use App\Entity\Feature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feature>
 */
class FeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feature::class);
    }

    public function update(Feature $feature): void
    {
        $this->getEntityManager()->persist($feature);
        $this->getEntityManager()->flush();
    }

    public function findByNameWithRules(string $name): ?Feature
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT f, r FROM App\Entity\Feature f
            JOIN f.rules r
            WHERE f.name=:name'
        )->setParameter('name', $name);
        return $query->getOneOrNullResult();
    }
}
