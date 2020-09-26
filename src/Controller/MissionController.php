<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Decouverte;
use App\Entity\Mission;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class MissionController extends EasyAdminController
{
    public function createMissionEntityFormBuilder($entity, $view) {
        $formBuilder = parent::createEntityFormBuilder($entity, $view);
        $fields = $formBuilder->all();
        /**
         * @var  $fieldId string
         * @var  $field FormBuilder
         */
        foreach ($fields as $fieldId => $field) {
          if ($fieldId == 'decouverte') {
            $options = [
                'attr'     => ['size' => 1,],
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'class'    => 'App\Entity\Decouverte',
                'choice_label' => 'nom'
            ];
            $formBuilder->add($fieldId, EntityType::class, $options);
          }
        }

        return $formBuilder;
    }

	/**
     * @Route("/aventure/{idAventure}/mission/{idMission}", name="missions")
     */
    public function mission_show(int $idAventure, int $idMission)
    {
    	$aventureRepository = $this->getDoctrine()->getRepository(Decouverte::class);
        $aventure = $aventureRepository->find($idAventure);

        $missionRepository = $this->getDoctrine()->getRepository(Mission::class);
        $mission = $missionRepository->find($idMission);	

        return $this->render('mission/mission.html.twig', [
           'aventure' => $aventure,
        	'mission' => $mission
        ]);
    }

    /**
     * @Route("/aventure/{idAventure}/missions", name="missions")
     */
    public function missions_show(int $idAventure)
    {
    	$decouverteRepository = $this->getDoctrine()->getRepository(Decouverte::class);
        $decouverte = $decouverteRepository->find($idAventure);  

        return $this->render('mission/missions.html.twig', [
           'decouverte' => $decouverte,
        ]);
    }
}
