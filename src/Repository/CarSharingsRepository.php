<?php

namespace App\Repository;

use App\Entity\CarSharings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CarSharingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarSharings::class);
    }

    /**
     * Recherche les trajets disponibles selon les critères : ville départ, ville arrivée, date.
     *
     * @param string|null $from
     * @param string|null $to
     * @param \DateTimeInterface|null $date
     * @param array $filters
     * @return CarSharings[]
     */
    public function searchByCriteria(?string $from, ?string $to, ?\DateTimeInterface $date, array $filters = []): array
    {
        if (!$from || !$to || !$date) {
            return [];
        }

        $start = new \DateTime($date->format('Y-m-d') . ' 00:00:00');
        $end = new \DateTime($date->format('Y-m-d') . ' 23:59:59');

        $qb = $this->createQueryBuilder('c')
            ->select('c', 'u', 'v')
            ->join('c.user', 'u')
            ->join('c.vehicle', 'v')
            ->where('(c.startAddress = :from OR c.startCity = :from)')
            ->andWhere('(c.endAddress = :to OR c.endCity = :to)')
            ->andWhere('c.dateStart BETWEEN :start AND :end')
            ->andWhere('c.availablePlaces > 0')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if (!empty($filters['eco'])) {
            $qb->andWhere('c.ecoFriendly = true');
        }

        if (!empty($filters['max_price'])) {
            $qb->andWhere('c.price <= :max_price')
                ->setParameter('max_price', $filters['max_price']);
        }

        if (!empty($filters['max_duration'])) {
            // TIMESTAMPDIFF en MySQL : durée entre deux timestamps en minutes
            $qb->andWhere("TIMESTAMPDIFF(MINUTE, c.dateStart, c.dateEnd) <= :max_duration")
                ->setParameter('max_duration', $filters['max_duration']);
        }

        if (!empty($filters['min_rating'])) {
            // Joindre les reviews pour calculer la note moyenne
            $qb->join('u.reviews', 'r')
                ->groupBy('c.id')
                ->having('AVG(r.note) >= :min_rating')
                ->setParameter('min_rating', $filters['min_rating']);
        }

        return $qb->getQuery()->getResult();
    }

    public function getClosestCarSharingAfterDate(string $from, string $to, \DateTimeInterface $date): ?CarSharings
    {
        return $this->createQueryBuilder('c')
            ->where('(c.startAddress = :from OR c.startCity = :from)')
            ->andWhere('(c.endAddress = :to OR c.endCity = :to)')
            ->andWhere('c.dateStart > :date')
            ->andWhere('c.availablePlaces > 0')
            ->andWhere('c.eco_friendly = true')
            ->orderBy('c.dateStart', 'ASC')
            ->setMaxResults(1)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
