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

    public function findOneByName(string $name): Feature
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function update(Feature $feature): void
    {
        $this->getEntityManager()->persist($feature);
        $this->getEntityManager()->flush();
    }
}
