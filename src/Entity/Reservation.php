<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_reservation;

    /**
     * @ORM\Column(type="string")
     */
    private $heure_reservation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;
  
  	/**
     * @ORM\OneToOne(targetEntity="App\Entity\Course", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $course;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): self
    {
        $this->date_reservation = $dateReservation;

        return $this;
    }

    public function getHeureReservation(): ?string
    {
        return $this->heure_reservation;
    }

    public function setHeureReservation(string $heureReservation): self
    {
        $this->heure_reservation = $heureReservation;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }   
  
  	public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): self
    {
        $this->course = $course;

        return $this;
    }   
}
