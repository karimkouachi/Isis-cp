<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function getAllNearEvents($nbDaysLimit){
        $dateNow = new \DateTime();
        $dateNowString = $dateNow->format('Y/m/d');
        $dateLimite = date('Y/m/d',strtotime('+'.$nbDaysLimit.' days',strtotime($dateNowString))) . PHP_EOL;

        return $this->createQueryBuilder('e')
            ->andWhere('e.date_debut BETWEEN :dateNow AND :dateLimite')
            ->setParameter('dateNow', $dateNowString)
            ->setParameter('dateLimite', $dateLimite)
            ->orderBy('e.date_debut', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getNearEventsLimit3($nbDaysLimit){
        $dateNow = new \DateTime();
        $dateNowString = $dateNow->format('Y/m/d');
        $dateLimite = date('Y/m/d',strtotime('+'.$nbDaysLimit.' days',strtotime($dateNowString))) . PHP_EOL;
        $limit = 3;

        return $this->createQueryBuilder('e')
            ->andWhere('e.date_debut BETWEEN :dateNow AND :dateLimite')
            ->setParameter('dateNow', $dateNowString)
            ->setParameter('dateLimite', $dateLimite)
            ->orderBy('e.date_debut', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Evenement[] Returns an array of Evenement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
