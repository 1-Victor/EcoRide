<?php

namespace App\Repository;

use App\Entity\PassengerConfirmation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PassengerConfirmation>
 */
class PassengerConfirmationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PassengerConfirmation::class);
    }

    // src/Repository/PassengerConfirmationRepository.php

    public function findValidatedSignalements(): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.carSharing', 'c')
            ->join('c.user', 'chauffeur')
            ->join('p.passenger', 'passager')
            ->addSelect('c', 'chauffeur', 'passager')
            ->where('p.status = :status')
            ->setParameter('status', 'validated')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PassengerConfirmation[] Returns an array of PassengerConfirmation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PassengerConfirmation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
