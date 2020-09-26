<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;

class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement", name="evenement")
     */
    public function index()
    {
        return $this->render('evenement/index.html.twig', [
            'controller_name' => 'EvenementController',
        ]);
    }

    /**
     * @Route("/evenement/{id}", name="evenement_show")
     */
    public function show(int $id)
    {
    	$evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
        $evenement = $evenementRepository->find($id);        

        return $this->render('evenement/evenement.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @Route("/evenements", name="evenements")
     */
    public function all()
    {
        $evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);

        $nbDaysLimit = "30";

        $evenements = $evenementRepository->getAllNearEvents($nbDaysLimit);   

        return $this->render('evenement/evenements.html.twig', [
            'evenements' => $evenements,
        ]);
    }
}
