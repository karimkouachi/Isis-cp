<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Excursion;

class ExcursionController extends AbstractController
{
    /**
     * @Route("/excursion/{id}", name="excursion_show")
     */
    public function show(int $id)
    {
    	$excursionRepository = $this->getDoctrine()->getRepository(Excursion::class);
        $excursion = $excursionRepository->find($id);        

        return $this->render('excursion/excursion.html.twig', [
            'excursion' => $excursion,
        ]);
    }
}
