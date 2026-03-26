<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Feature\Feature;
use App\Dto\FeatureQuery;
use App\Domain\Feature\Exception\FeatureNotFoundException;
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

    public function save(string $name, Feature $feature): Feature
    {
        $existing = $this->findOneBy(['name' => $name]);
        $doctrineFeature = $this->mapper->toPersistence($feature, $existing);
        $this->getEntityManager()->persist($doctrineFeature);
        $this->getEntityManager()->flush();
        return $this->mapper->toDomain($doctrineFeature);
    }

    public function findByNameWithRules(string $name): ?Feature
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT f, r FROM App\Infrastructure\Persistence\DoctrineFeature f
            LEFT JOIN f.rules r
            WHERE f.name=:name'
        )->setParameter('name', $name);
        $result = $query->getOneOrNullResult();
        return $result ? $this->mapper->toDomain($result) : null;
    }

    /** @return Feature[] */
    public function findPaginated(FeatureQuery $featureQuery): array
    {
        $db = $this->createQueryBuilder('f');
        $db->distinct();
        $db->leftJoin('f.rules', 'r');
        $db->addSelect('r');
        if (!empty($featureQuery->search)) {
            $db->andWhere('f.name LIKE :search')
            ->setParameter('search', '%'. $featureQuery->search . '%');
        }
        $db->orderBy('f.' . $featureQuery->sortBy, $featureQuery->sortDir);
        $db->setFirstResult(($featureQuery->page - 1) * $featureQuery->limit);
        $db->setMaxResults($featureQuery->limit);

        $results = $db->getQuery()->getResult();
        $unique = array_unique($results, SORT_REGULAR);
        return $results
            ? array_map(fn($result) => $this->mapper->toDomain($result), $unique)
            : [];
    }

    public function delete(string $name): void
    {
        $affected = $this->getEntityManager()->createQuery(
            'DELETE FROM App\Infrastructure\Persistence\DoctrineFeature f WHERE f.name=:name'
        )->setParameter('name', $name)->execute();
        if ($affected === 0) throw new FeatureNotFoundException($name);
    }

    public function toggle(string $name): Feature
    {
        $this->getEntityManager()->getConnection()->executeStatement(
            'UPDATE feature SET enabled = NOT enabled WHERE name = :name',
            ['name' => $name]
        );
        $doctrineFeature = $this->findOneBy(['name' => $name]);
        return $doctrineFeature ? $this->mapper->toDomain($doctrineFeature) : throw new FeatureNotFoundException($name);
    }
}
