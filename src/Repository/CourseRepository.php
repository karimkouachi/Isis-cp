<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    protected $em;
    protected $token_storage;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em, TokenStorageInterface $token_storage)
    {
        parent::__construct($registry, Course::class);
        $this->em = $em;
        $this->token_storage = $token_storage;
    }

    public function enregistre($post, $type, $prix){

        $course = new Course();
        $course->setType($type);
        $course->setDatePriseEnCharge(\DateTime::createFromFormat("d/m/Y H:i:s", $post['date_prise_en_charge']." 00:00:00"));// (format actuel, date string)
        $course->setHeurePriseEnCharge($post['heure_prise_en_charge']);
        $course->setNbPassagers($post['nb_passagers']);
        $course->setNbBagages($post['nb_bagages']);
        $course->setAdresseDepart($post['adresse_depart']);
        $course->setAdresseArrivee($post['adresse_arrivee']);

        $dureeMinutes = ($post['duree']/60);
        $course->setDuree($dureeMinutes);

        $course->setPrix($prix);

        $this->em->persist($course);
        $this->em->flush();

        return $course;
    }

    // /**
    //  * @return Course[] Returns an array of Course objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
