<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Decouverte;

class DecouverteController extends AbstractController
{
    public function show(/*int $id*/)
    {
    	/*$decouverteRepository = $this->getDoctrine()->getRepository(Decouverte::class);
        $decouverte = $decouverteRepository->find($id);        

        return $this->render('decouverte/decouverte.html.twig', [
            'decouverte' => $decouverte,
        ]);*/
    }
}
