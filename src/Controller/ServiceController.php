<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;
use App\Entity\Decouverte;
use App\Entity\Excursion;

class ServiceController extends AbstractController
{
  	/**
     * @Route("/services", name="services")
     */
    public function index()
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
  
    /**
     * @Route("/service/transfert", name="transfert")
     */
    public function transfert()
    {
        return $this->render('service/transfert.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    /**
     * @Route("/service/transport", name="transport")
     */
    public function transport()
    {
        return $this->render('service/transport.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    /**
     * @Route("/service/decouverte", name="decouverte")
     */
    public function decouverte()
    {
        $aventureRepository = $this->getDoctrine()->getRepository(Decouverte::class);
        $aventures = $aventureRepository->findAll(); 

        return $this->render('service/decouverte.html.twig', [
            'aventures' => $aventures,
        ]);
    }

    /**
     * @Route("/service/excursion", name="excursion")
     */
    public function excursion()
    {
        $excursionRepository = $this->getDoctrine()->getRepository(Excursion::class);
        $excursions = $excursionRepository->findAll(); 
        
        return $this->render('service/excursion.html.twig', [
            'excursions' => $excursions,
        ]);
    }

    /**
     * @Route("/service/mariage", name="mariage")
     */
    public function mariage()
    {
        return $this->render('service/mariage.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    /**
     * @Route("/service/evenement", name="evenement")
     */
    public function evenement()
    {
        $evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
        $nbDaysLimit = "30";
        $evenements = $evenementRepository->getNearEventsLimit3($nbDaysLimit); 

        return $this->render('service/evenement.html.twig', [
            'evenements' => $evenements,
        ]);
    }
}
