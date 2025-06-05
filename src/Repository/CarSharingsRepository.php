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
            $qb->andWhere('c.eco_friendly = true');
        }

        if (!empty($filters['max_price'])) {
            $qb->andWhere('c.price <= :max_price')
                ->setParameter('max_price', $filters['max_price']);
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

    public function countCarSharingsByDay(): array
    {
        return $this->createQueryBuilder('c')
            ->select("SUBSTRING(c.dateStart, 1, 10) AS jour, COUNT(c.id) AS nb")
            ->groupBy('jour')
            ->orderBy('jour', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalCarSharingsCount(): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countCancelledCarSharingsByDay(): array
    {
        return $this->createQueryBuilder('c')
            ->select("SUBSTRING(c.updated_at, 1, 10) AS jour, COUNT(c.id) AS nb")
            ->join('c.status', 's')
            ->where('s.status = :cancelled')
            ->setParameter('cancelled', 'Annulé')
            ->groupBy('jour')
            ->orderBy('jour', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
