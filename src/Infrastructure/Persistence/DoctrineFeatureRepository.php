<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Feature\Feature;
use App\Infrastructure\Persistence\DoctrineFeature;
use App\Domain\Feature\FeatureRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineFeatureRepository extends ServiceEntityRepository implements FeatureRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private FeatureMapper $mapper)
    {
        parent::__construct($registry, DoctrineFeature::class);
    }

    public function save(Feature $feature): void
    {
        $existing = $this->find($feature->id());
        $feature = $this->mapper->toPersistence($feature, $existing);
        $this->getEntityManager()->persist($feature);
        $this->getEntityManager()->flush();
    }

    public function findByNameWithRules(string $name): ?Feature
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT f, r FROM App\Infrastructure\Persistence\DoctrineFeature f
            JOIN f.rules r
            WHERE f.name=:name'
        )->setParameter('name', $name);
        $result = $query->getOneOrNullResult();
        return $result ? $this->mapper->toDomain($result) : null;
    }
}
