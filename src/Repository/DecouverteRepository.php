<?php

namespace App\Repository;

use App\Entity\Decouverte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Decouverte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Decouverte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Decouverte[]    findAll()
 * @method Decouverte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DecouverteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Decouverte::class);
    }

    // /**
    //  * @return Decouverte[] Returns an array of Decouverte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Decouverte
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
