<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class SerieRepository
 * @package App\Repository
 * @codeCoverageIgnore
 */
class SeriesRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Series::class);
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(): int
    {
        $count = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('count(s) as series')
            ->from(Serie::class, 's')
            ->getQuery()
            ->getSingleScalarResult();

        return (int)$count;
    }

    /**
     * @param int|null $id
     * @return Series|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFullyLoadedSeriesById(?int $id):? Series
    {
        if (null === $id) {
            return null;
        }

        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('series, type, season, episode')
            ->from(Series::class, 'series')
            ->leftJoin('series.type', 'type')
            ->leftJoin('series.seasons', 'season')
            ->leftJoin('season.episodes', 'episode')
            ->where('series.id = :id')
            ->setParameter('id', $id)
            ->addOrderBy('season.rank', 'ASC')
            ->addOrderBy('episode.rank', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneById(int $id):? Series
    {
        return $this
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
