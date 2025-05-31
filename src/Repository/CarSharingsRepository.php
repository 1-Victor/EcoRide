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
     * @param string|null
     * @param string|null
     * @param \DateTimeInterface|null
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
            ->join('c.user', 'u')
            ->join('c.vehicle', 'v')
            ->addSelect('u', 'v')
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

        $results = $qb->getQuery()->getResult();

        return array_filter($results, function ($covoiturage) use ($filters) {
            $valid = true;

            if (!empty($filters['max_duration'])) {
                $duration = $covoiturage->getDateStart()->diff($covoiturage->getDateEnd());
                $minutes = ($duration->h * 60) + $duration->i;
                $valid = $valid && ($minutes <= $filters['max_duration']);
            }

            if (!empty($filters['min_rating'])) {
                $notes = array_map(fn($r) => $r->getNote(), $covoiturage->getUser()->getReviews()->toArray());
                $notes = array_filter($notes);
                $avg = count($notes) > 0 ? array_sum($notes) / count($notes) : 0;
                $valid = $valid && ($avg >= $filters['min_rating']);
            }

            return $valid;
        });
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
