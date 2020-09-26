<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CourseRepository")
 */
class Course
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse_depart;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse_arrivee;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $heure_prise_en_charge;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_prise_en_charge;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $duree;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_passagers;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_bagages;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAdresseDepart(): ?string
    {
        return $this->adresse_depart;
    }

    public function setAdresseDepart(string $adresse_depart): self
    {
        $this->adresse_depart = $adresse_depart;

        return $this;
    }

    public function getAdresseArrivee(): ?string
    {
        return $this->adresse_arrivee;
    }

    public function setAdresseArrivee(string $adresse_arrivee): self
    {
        $this->adresse_arrivee = $adresse_arrivee;

        return $this;
    }

    public function getHeurePriseEnCharge(): ?string
    {
        return $this->heure_prise_en_charge;
    }

    public function setHeurePriseEnCharge(string $heure_prise_en_charge): self
    {
        $this->heure_prise_en_charge = $heure_prise_en_charge;

        return $this;
    }

    public function getDatePriseEnCharge(): ?\DateTimeInterface
    {
        return $this->date_prise_en_charge;
    }

    public function setDatePriseEnCharge(\DateTimeInterface $date_prise_en_charge): self
    {
        $this->date_prise_en_charge = $date_prise_en_charge;

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(string $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNbPassagers(): ?int
    {
        return $this->nb_passagers;
    }

    public function setNbPassagers(int $nb_passagers): self
    {
        $this->nb_passagers = $nb_passagers;

        return $this;
    }

    public function getNbBagages(): ?int
    {
        return $this->nb_bagages;
    }

    public function setNbBagages(int $nb_bagages): self
    {
        $this->nb_bagages = $nb_bagages;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
}
