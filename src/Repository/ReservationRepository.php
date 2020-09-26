<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{ 
  	protected $em;
    protected $token_storage;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, TokenStorageInterface $token_storage)
    {
        parent::__construct($registry, Reservation::class);
        $this->em = $em;
        $this->token_storage = $token_storage;
    }

    public function enregistre($post, $course){
        $utilisateur = $this->token_storage->getToken()->getUser();

        $reservation = new Reservation();

        date_default_timezone_set('Europe/Paris');
        $reservation->setDateReservation(new \DateTime('now'));

        $strtotime = strtotime(date('Y-m-d H:i:s'));
        $reservation->setHeureReservation(date('H:i:s', $strtotime));
        $reservation->setUtilisateur($utilisateur);
        $reservation->setCourse($course);

        $this->em->persist($reservation);
        $this->em->flush();
    }

    // /**
    //  * @return Reservation[] Returns an array of Reservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
