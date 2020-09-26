<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Course;
use App\Entity\Reservation;
use App\Entity\Evenement;
use App\Entity\Decouverte;
use App\Entity\Mission;
use App\Entity\Excursion;
use App\Form\ReservationType;
use App\Service\Calcul;
use App\Service\Email;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="reservation")
     */
    public function index()
    {
    	
		/*$reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);*/

        /*$dateResa = [
        	array(
        		'date' => '17/10/2019',
        		'heure' => 14,
        		'minute' => 10,
        		'temps' => 60
        	)
        ];*/


        return $this->render('reservation/index.html.twig', [
            /*'dateResa' => $dateResa*/
        ]);
    }

    public function transfert()
    {     

        return $this->render('reservation/transfert.html.twig');
    }

    public function infos_transfert(Request $request, Calcul $calcul)
    {
        $centreZone2 = ['latitude' => 47.208015, 'longitude' => -1.552916];
        $rayonZone1 = 1.98;
        $rayonZone2 = 5.42;

        // savoir si une adresse est en hors zone (prix_base = 1.69) sinon prix_base = 1.77
        $distanceDepartAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));

        $localiseDepart['cercle2'] = $calcul->adresseInCercle2($distanceDepartAcentreZone2, $rayonZone2);

        $localiseDepart['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);

        $distanceArriveeAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));

        $localiseArrivee['cercle2'] = $calcul->adresseInCercle2($distanceArriveeAcentreZone2, $rayonZone2);

        $localiseArrivee['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);

        if(
            (
                !$localiseDepart['cercle2'] &&
                !$localiseDepart['carre1Zone2'] &&
                !$localiseDepart['carre2Zone2'] &&
                !$localiseDepart['carre3Zone2'] &&
                !$localiseDepart['carre4Zone2'] &&
                !$localiseDepart['carre5Zone2']
            ) 
            ||
            (
                !$localiseArrivee['cercle2'] && 
                !$localiseArrivee['carre1Zone2'] &&
                !$localiseArrivee['carre2Zone2'] &&
                !$localiseArrivee['carre3Zone2'] &&
                !$localiseArrivee['carre4Zone2'] &&
                !$localiseArrivee['carre5Zone2']
            )
        ){ 
            $prix_base = 1.69;
        }else{
            $prix_base = 1.77;
        }


        $distanceParRoute = explode(" km", $_POST['distanceParRoute'])[0];
        $distanceParRoute = str_replace(",", ".", $distanceParRoute);
        $distanceParRoute = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $distanceParRoute);
        $distanceParRoute = floatval($distanceParRoute);


        $nb_minutes = $_POST['duree'] / 60;

        $_POST['date_prise_en_charge'] = str_replace('/', '-', $_POST['date_prise_en_charge']);
      
        if (
            intval(explode(":", $_POST['heure_prise_en_charge'])[0]) > 19 || intval(explode(":", $_POST['heure_prise_en_charge'])[0]) < 8 || date("N", strtotime($_POST['date_prise_en_charge'])) == 6 || date("N", strtotime($_POST['date_prise_en_charge'])) == 7
            // le jour est férié
        ) {
            $majoration = 1.25;
          }else{
            $majoration = 1;
          }
        
        $_POST['date_prise_en_charge'] = str_replace('-', '/', $_POST['date_prise_en_charge']);

        $prix = (4 + ($prix_base * $distanceParRoute) + (0.35 * $nb_minutes)) * $majoration;
      
        if($prix < 25){
            $prix = 25;
        }

        return $this->render('reservation/informations_transfert.html.twig', [
            'infosReservation' => $_POST,
            'prix' => $prix
        ]);
    }

    public function reservation_transfert_email(Request $request, Email $email, Calcul $calcul)
    {
        $centreZone2 = ['latitude' => 47.208015, 'longitude' => -1.552916];
        $rayonZone1 = 1.98;
        $rayonZone2 = 5.42;

        // savoir si une adresse est en hors zone (prix_base = 1.69) sinon prix_base = 1.77
        $distanceDepartAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));

        $localiseDepart['cercle2'] = $calcul->adresseInCercle2($distanceDepartAcentreZone2, $rayonZone2);

        $localiseDepart['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);

        $distanceArriveeAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));

        $localiseArrivee['cercle2'] = $calcul->adresseInCercle2($distanceArriveeAcentreZone2, $rayonZone2);

        $localiseArrivee['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);

        if(
            (
                !$localiseDepart['cercle2'] &&
                !$localiseDepart['carre1Zone2'] &&
                !$localiseDepart['carre2Zone2'] &&
                !$localiseDepart['carre3Zone2'] &&
                !$localiseDepart['carre4Zone2'] &&
                !$localiseDepart['carre5Zone2']
            ) 
            ||
            (
                !$localiseArrivee['cercle2'] && 
                !$localiseArrivee['carre1Zone2'] &&
                !$localiseArrivee['carre2Zone2'] &&
                !$localiseArrivee['carre3Zone2'] &&
                !$localiseArrivee['carre4Zone2'] &&
                !$localiseArrivee['carre5Zone2']
            )
        ){ 
            $prix_base = 1.69;
        }else{
            $prix_base = 1.77;
        }


        $distanceParRoute = explode(" km", $_POST['distanceParRoute'])[0];
        $distanceParRoute = str_replace(",", ".", $distanceParRoute);
        $distanceParRoute = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $distanceParRoute);
        $distanceParRoute = floatval($distanceParRoute);


        $nb_minutes = $_POST['duree'] / 60;

        $_POST['date_prise_en_charge'] = str_replace('/', '-', $_POST['date_prise_en_charge']);
      
        if (
            intval(explode(":", $_POST['heure_prise_en_charge'])[0]) > 19 || intval(explode(":", $_POST['heure_prise_en_charge'])[0]) < 8 || date("N", strtotime($_POST['date_prise_en_charge'])) == 6 || date("N", strtotime($_POST['date_prise_en_charge'])) == 7
            // le jour est férié
        ) {
            $majoration = 1.25;
          }else{
            $majoration = 1;
          }
        
        $_POST['date_prise_en_charge'] = str_replace('-', '/', $_POST['date_prise_en_charge']);

        $_POST['prix'] = (4 + ($prix_base * $distanceParRoute) + (0.35 * $nb_minutes)) * $majoration;
      
        if($_POST['prix'] < 25){
            $_POST['prix'] = 25;
        }

        $courseRepository = $this->getDoctrine()->getRepository(Course::class);
        $course = $courseRepository->enregistre($_POST, "transfert", $_POST['prix']);

        $reservationRepository = $this->getDoctrine()->getRepository(Reservation::class);
        $reservationRepository->enregistre($_POST, $course);

        $utilisateur = $this->getUser();
      
        //SWIFTMAILER
        $infosMail = [
            'prix_base' => $prix_base,
            'distanceParRoute' => $distanceParRoute,
            'nb_minutes' => $nb_minutes,
            'majoration' => $majoration,
            'prestation' => "transfert",
            'civilite' => $_POST['civilite'],
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'adresse' => $utilisateur->getAdresse(),
            'tel' => $_POST['tel'],
            'email' => $_POST['email'],
            'date_prise_en_charge' => $_POST['date_prise_en_charge'],
            'heure_prise_en_charge' => $_POST['heure_prise_en_charge'],
            'adresse_depart' => $_POST['adresse_depart'],
            'adresse_arrivee' => $_POST['adresse_arrivee'],
            'forfait' => $_POST['forfait'],
            'prix' => $_POST['prix'],
            'nb_passagers' => $_POST['nb_passagers'],
            'nb_bagages' => $_POST['nb_bagages']
        ];
        
        $email->send($infosMail);

        return $this->forward('App\Controller\HelloController::Home', [
            'reservation' => true
        ]); 
    }
  
  	public function evenement(int $id)
    {
    	
		$evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
        $evenement = $evenementRepository->find($id);		

        return $this->render('reservation/evenement.html.twig', [
        	'evenement' => $evenement
        ]);
    }
  
  	/**
     * @Route("/reservation/informations/evenement/{id}", name="reservation_infos_evenement")
     */
    public function infos_evenement(Request $request, Calcul $calcul, $id)
    {
    	$evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
        $evenement = $evenementRepository->find($id); 

        $centreZone2 = ['latitude' => 47.208015, 'longitude' => -1.552916];
        $rayonZone1 = 1.98;
        $rayonZone2 = 5.42;

        // savoir si une adresse est en hors zone (prix_base = 1.69) sinon prix_base = 1.77
        $distanceDepartAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));

        $localiseDepart['cercle2'] = $calcul->adresseInCercle2($distanceDepartAcentreZone2, $rayonZone2);

        $localiseDepart['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);

        $distanceArriveeAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));

        $localiseArrivee['cercle2'] = $calcul->adresseInCercle2($distanceArriveeAcentreZone2, $rayonZone2);

        $localiseArrivee['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);

        if(
            (
                !$localiseDepart['cercle2'] &&
                !$localiseDepart['carre1Zone2'] &&
                !$localiseDepart['carre2Zone2'] &&
                !$localiseDepart['carre3Zone2'] &&
                !$localiseDepart['carre4Zone2'] &&
                !$localiseDepart['carre5Zone2']
            ) 
            ||
            (
                !$localiseArrivee['cercle2'] && 
                !$localiseArrivee['carre1Zone2'] &&
                !$localiseArrivee['carre2Zone2'] &&
                !$localiseArrivee['carre3Zone2'] &&
                !$localiseArrivee['carre4Zone2'] &&
                !$localiseArrivee['carre5Zone2']
            )
        ){ 
            $prix_base = 1.69;
        }else{
            $prix_base = 1.77;
        }


        $distanceParRoute = explode(" km", $_POST['distanceParRoute'])[0];
        $distanceParRoute = str_replace(",", ".", $distanceParRoute);
        $distanceParRoute = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $distanceParRoute);
        $distanceParRoute = floatval($distanceParRoute);


        $nb_minutes = $_POST['duree'] / 60;

	  	$_POST['date_prise_en_charge'] = str_replace('/', '-', $_POST['date_prise_en_charge']);
	  
        if (
            intval(explode(":", $_POST['heure_prise_en_charge'])[0]) > 19 || intval(explode(":", $_POST['heure_prise_en_charge'])[0]) < 8 || date("N", strtotime($_POST['date_prise_en_charge'])) == 6 || date("N", strtotime($_POST['date_prise_en_charge'])) == 7
            // le jour est férié
        ) {
            $majoration = 1.25;
		  }else{
            $majoration = 1;
		  }
	  	
	  	$_POST['date_prise_en_charge'] = str_replace('-', '/', $_POST['date_prise_en_charge']);

        $prix = (4 + ($prix_base * $distanceParRoute) + (0.35 * $nb_minutes)) * $majoration;
	  
	  	if($prix < 25){
			$prix = 25;
	  	}

    	return $this->render('reservation/informations_evenement.html.twig', [
        	'evenement' => $evenement,
        	'infosReservation' => $_POST,
            'prix' => $prix
        ]);
    }
  
  	/**
     * 
     *
     * @Route("/reservation/evenement/{id}/email", name="reservation_evenement_email")
     */
    public function reservation_evenement_email(Request $request, Email $email, $id, Calcul $calcul)
    {
    	$evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
        $evenement = $evenementRepository->find($id); 

        $centreZone2 = ['latitude' => 47.208015, 'longitude' => -1.552916];
        $rayonZone1 = 1.98;
        $rayonZone2 = 5.42;

        // savoir si une adresse est en hors zone (prix_base = 1.69) sinon prix_base = 1.77
        $distanceDepartAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));

        $localiseDepart['cercle2'] = $calcul->adresseInCercle2($distanceDepartAcentreZone2, $rayonZone2);

        $localiseDepart['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
        $localiseDepart['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);

        $distanceArriveeAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));

        $localiseArrivee['cercle2'] = $calcul->adresseInCercle2($distanceArriveeAcentreZone2, $rayonZone2);

        $localiseArrivee['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
        $localiseArrivee['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);

        if(
            (
                !$localiseDepart['cercle2'] &&
                !$localiseDepart['carre1Zone2'] &&
                !$localiseDepart['carre2Zone2'] &&
                !$localiseDepart['carre3Zone2'] &&
                !$localiseDepart['carre4Zone2'] &&
                !$localiseDepart['carre5Zone2']
            ) 
            ||
            (
                !$localiseArrivee['cercle2'] && 
                !$localiseArrivee['carre1Zone2'] &&
                !$localiseArrivee['carre2Zone2'] &&
                !$localiseArrivee['carre3Zone2'] &&
                !$localiseArrivee['carre4Zone2'] &&
                !$localiseArrivee['carre5Zone2']
            )
        ){ 
            $prix_base = 1.69;
        }else{
            $prix_base = 1.77;
        }

        $distanceParRoute = explode(" km", $_POST['distanceParRoute'])[0];
        $distanceParRoute = str_replace(",", ".", $distanceParRoute);
        $distanceParRoute = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $distanceParRoute);
        $distanceParRoute = floatval($distanceParRoute);

        $nb_minutes = $_POST['duree'] / 60;

        $_POST['date_prise_en_charge'] = str_replace('/', '-', $_POST['date_prise_en_charge']);
	  
        if (
            intval(explode(":", $_POST['heure_prise_en_charge'])[0]) > 19 || intval(explode(":", $_POST['heure_prise_en_charge'])[0]) < 8 || date("N", strtotime($_POST['date_prise_en_charge'])) == 6 || date("N", strtotime($_POST['date_prise_en_charge'])) == 7
            // le jour est férié
        ) {
            $majoration = 1.25;
		  }else{
            $majoration = 1;
		  }
	  	
	  	$_POST['date_prise_en_charge'] = str_replace('-', '/', $_POST['date_prise_en_charge']);

        $prix = (4+ ($prix_base * $distanceParRoute) + (0.35 * $nb_minutes)) * $majoration;
	  
	  	if($prix < 25){
			$prix = 25;
	  	}

        $courseRepository = $this->getDoctrine()->getRepository(Course::class);
        $course = $courseRepository->enregistre($_POST, "evenement", $prix);

        $reservationRepository = $this->getDoctrine()->getRepository(Reservation::class);
        $reservationRepository->enregistre($_POST, $course);

        $utilisateur = $this->getUser();

    	//SWIFTMAILER
        $infosMail = [
        	'prix_base' => $prix_base,
        	'distanceParRoute' => $distanceParRoute,
        	'nb_minutes' => $nb_minutes,
        	'majoration' => $majoration,
        	'prestation' => "evenement",
        	'evenement' => $evenement->getNom(),
            'genre' => $evenement->getGenre(),
            'civilite' => $_POST['civilite'],
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'adresse' => $utilisateur->getAdresse(),
            'tel' => $_POST['tel'],
            'email' => $_POST['email'],
            'date_prise_en_charge' => $_POST['date_prise_en_charge'],
            'heure_prise_en_charge' => $_POST['heure_prise_en_charge'],
            'adresse_depart' => $_POST['adresse_depart'],
            'adresse_arrivee' => $_POST['adresse_arrivee'],
			'prix' => $prix,
            'nb_passagers' => $_POST['nb_passagers'],
            'nb_bagages' => $_POST['nb_bagages']
        ];

        $email->send($infosMail);

        return $this->forward('App\Controller\HelloController::Home', [
        	'reservation' => true
	    ]); 
    }

    /**
     * @Route("/reservation/transport", name="reservation_transport")
     */
    public function transport()
    {
    	
		/*$reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);*/

        /*$dateResa = [
        	array(
        		'date' => '17/10/2019',
        		'heure' => 14,
        		'minute' => 10,
        		'temps' => 60
        	)
        ];*/


        return $this->render('reservation/transport.html.twig', [
            /*'dateResa' => $dateResa*/
        ]);
    }

    /**
     * @Route("/reservation/excursion/{id}", name="reservation_excursion")
     */
    public function excursion($id)
    {
    	
		/*$reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);*/

        /*$dateResa = [
        	array(
        		'date' => '17/10/2019',
        		'heure' => 14,
        		'minute' => 10,
        		'temps' => 60
        	)
        ];*/

        $excursionRepository = $this->getDoctrine()->getRepository(Excursion::class);
        $excursion = $excursionRepository->find($id); 


        return $this->render('reservation/excursion.html.twig', [
            'excursion' => $excursion
        ]);
    }

    /**
     * @Route("/reservation/aventure/{idAventure}/mission/{idMission}", name="reservation_aventure")
     */
    public function aventure(int $idAventure, int $idMission)
    {
		$aventureRepository = $this->getDoctrine()->getRepository(Decouverte::class);
        $aventure = $aventureRepository->find($idAventure);

        $missionRepository = $this->getDoctrine()->getRepository(Mission::class);
        $mission = $missionRepository->find($idMission);			

        return $this->render('reservation/aventure.html.twig', [
        	'aventure' => $aventure,
        	'mission' => $mission
        ]);
    }

    /**
     * @Route("/getReservationsToday", name="getReservationsToday")
     */
    public function getReservationsToday(Request $request)
    {	
    	$date_prise_en_charge = explode('/', $request->query->get('date_prise_en_charge'));
		$dateUS = $date_prise_en_charge[2] . '-' . $date_prise_en_charge[1] . '-' . $date_prise_en_charge[0];
		/*$time = strtotime($dateUS);
		 date('Y-m-d',$time);*/
		 /*$date = DateTime::createFromFormat('d/m/Y H:i', "28/07/2016 10:00");
		$date = $date->format('Y-m-d H:i:s');*/
		$newformat = \DateTime::createFromFormat("Y-m-d H:i:s", $dateUS." 00:00:00");

		$reservationRepository = $this->getDoctrine()->getRepository(Reservation::class);
        $reservations = $reservationRepository->findBy(array('date_prise_en_charge' => $newformat)); 

        $output = array();

        if(!empty($reservations)){
        	foreach ($reservations as $key => $reservation){
	            $output[$key]=array(
	            	"id" => $reservation->getId(),
	            	"date_prise_en_charge" => $reservation->getDatePriseEnCharge(),
	            	"heure_prise_en_charge" => $reservation->getHeurePriseEnCharge(),
	            	"adresseDepart" => $reservation->getAdresseDepart(),
	            	"adresseArrivee" => $reservation->getAdresseArrivee(),
	            	"duree" => $reservation->getDuree()
	            );
	        }
        }
        
        /*$encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
		$serializer = new Serializer($normalizers, $encoders);

		$jsonReservations = $serializer->serialize($reservations, 'json');*/

        return new JsonResponse($output);
    }

    /**
     * @Route("/reservation/informations/{prestation}/{id}", name="reservation_presta_infos")
     */
    public function presta_infos(Calcul $calcul, Request $request, $prestation, $id)
    {
    	$prestationType = $prestation;
		if($prestation == "evenement"){
    		$prestationRepository = $this->getDoctrine()->getRepository(Evenement::class);
    	}else if($prestation == "decouverte"){
    		$prestationRepository = $this->getDoctrine()->getRepository(Decouverte::class);
    	}
        $prestation = $prestationRepository->find($id); 
        $prestation->type = $prestationType;

    	$centreZone1 = ['latitude' => 47.217935, 'longitude' => -1.543369];
		$centreZone2 = ['latitude' => 47.208015, 'longitude' => -1.552916];
		$centreAeroport = ['latitude' => 47.15741879999999, 'longitude' => -1.606231600000001];
		$rayonZone1 = 1.98;
		$rayonZone2 = 5.42;
		$rayonAeroport = 1;
		$zone = 0;
		$distanceParRoute = explode(" km", $_POST['distanceParRoute'])[0];
		$distanceParRoute = str_replace(",", ".", $distanceParRoute);
		$distanceParRoute = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $distanceParRoute);
		$distanceParRoute = floatval($distanceParRoute);

		$latLngAdresseDepart = [
			'latitude' => floatval($_POST['latitude_depart']),
			'longitude' => floatval($_POST['longitude_depart'])
		];

		$latLngAdresseArrivee = [
			'latitude' => floatval($_POST['latitude_arrivee']),
			'longitude' => floatval($_POST['longitude_arrivee'])
		];
		
		//DEPART
		$distanceDepartAcentreZone1 = $calcul->distancePoints($centreZone1['latitude'], $centreZone1['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));
		$distanceDepartAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));
		$distanceDepartAeroport = $calcul->distancePoints($centreAeroport['latitude'], $centreAeroport['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));
		//ARRIVEE
		$distanceArriveeAcentreZone1 = $calcul->distancePoints($centreZone1['latitude'], $centreZone1['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));
		$distanceArriveeAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));
		$distanceArriveeAeroport = $calcul->distancePoints($centreAeroport['latitude'], $centreAeroport['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));

		//DEPART
        $localiseDepart['cercle1'] = $calcul->adresseInCercle1($distanceDepartAcentreZone1, $rayonZone1);
		$localiseDepart['cercle2'] = $calcul->adresseInCercle2($distanceDepartAcentreZone2, $rayonZone2);
		//ARRIVEE
		$localiseArrivee['cercle1'] = $calcul->adresseInCercle1($distanceArriveeAcentreZone1, $rayonZone1);
		$localiseArrivee['cercle2'] = $calcul->adresseInCercle2($distanceArriveeAcentreZone2, $rayonZone2);

		//DEPART
		$localiseDepart['carre1Zone1'] = $calcul->adresseInCarre1Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre2Zone1'] = $calcul->adresseInCarre2Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre3Zone1'] = $calcul->adresseInCarre3Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre4Zone1'] = $calcul->adresseInCarre4Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre5Zone1'] = $calcul->adresseInCarre5Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);

		$localiseDepart['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);

		//ARRIVEE
		$localiseArrivee['carre1Zone1'] = $calcul->adresseInCarre1Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre2Zone1'] = $calcul->adresseInCarre2Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre3Zone1'] = $calcul->adresseInCarre3Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre4Zone1'] = $calcul->adresseInCarre4Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre5Zone1'] = $calcul->adresseInCarre5Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);

		$localiseArrivee['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);

		//DEPART
        $localiseDepart['aeroport'] = $calcul->adresseInAeroport($distanceDepartAeroport, $rayonAeroport);

		//ARRIVEE
        $localiseArrivee['aeroport'] = $calcul->adresseInAeroport($distanceArriveeAeroport, $rayonAeroport);

		//DEPART
		if(
			$localiseDepart['cercle1'] ||
			$localiseDepart['carre1Zone1'] ||
			$localiseDepart['carre2Zone1'] ||
			$localiseDepart['carre3Zone1'] ||
			$localiseDepart['carre4Zone1'] ||
			$localiseDepart['carre5Zone1']
		){
			$zoneDepart = "1";
		}elseif (
			$localiseDepart['cercle2'] ||
			$localiseDepart['carre1Zone2'] ||
			$localiseDepart['carre2Zone2'] ||
			$localiseDepart['carre3Zone2'] ||
			$localiseDepart['carre4Zone2'] ||
			$localiseDepart['carre5Zone2']
		){
			$zoneDepart = "2";
		}elseif($localiseDepart['aeroport']){
			$zoneDepart = "3"; // AEROPORT
		}else{
			$zoneDepart = "4"; // HORS ZONE
		}

		//ARRIVEE
		if(
			$localiseArrivee['cercle1'] ||
			$localiseArrivee['carre1Zone1'] ||
			$localiseArrivee['carre2Zone1'] ||
			$localiseArrivee['carre3Zone1'] ||
			$localiseArrivee['carre4Zone1'] ||
			$localiseArrivee['carre5Zone1']
		){
			$zoneArrivee = "1";
		}elseif (
			$localiseArrivee['cercle2'] ||
			$localiseArrivee['carre1Zone2'] ||
			$localiseArrivee['carre2Zone2'] ||
			$localiseArrivee['carre3Zone2'] ||
			$localiseArrivee['carre4Zone2'] ||
			$localiseArrivee['carre5Zone2']
		){
			$zoneArrivee = "2";
		}elseif($localiseArrivee['aeroport']){
			$zoneArrivee = "3"; // AEROPORT
		}else{
			$zoneArrivee = "4"; // HORS ZONE
		}
        
        echo '<p>Distance entre la zone 1 et le depart: '.($distanceDepartAcentreZone1).' Km</p>';
        echo '<p>Distance entre la zone 2 et le depart: '.($distanceDepartAcentreZone2).' Km</p>';

        echo '<p>Distance entre la zone 1 et larrivee: '.($distanceArriveeAcentreZone1).' Km</p>';
        echo '<p>Distance entre la zone 2 et larrivee: '.($distanceArriveeAcentreZone2).' Km</p>';

        echo '<p>Distance entre aeroport et le depart: '.($distanceDepartAeroport).' Km</p>';
        echo '<p>Distance entre aeroport et larrivee: '.($distanceArriveeAeroport).' Km</p>';

        echo '<p>Zone depart: '.$zoneDepart.'</p>';
        echo '<p>Zone arrivée: '.$zoneArrivee.'</p>';

        $distanceAdressesVol = $calcul->distancePoints(floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']), floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));
        $prix = $calcul->prix($zoneDepart, $zoneArrivee, $distanceParRoute);

        echo '<p>Prix: '.$prix.'</p>';

        var_dump($localiseDepart);
        var_dump($localiseArrivee);

        echo '<p>Depart dans aeroport: '.$localiseDepart['aeroport'].'</p>';
        echo '<p>Arrivee dans aeroport: '.$localiseArrivee['aeroport'].'</p>';

        echo '<p>lat depart: '.$_POST['latitude_depart'].'</p>';
        echo '<p>lng depart: '.$_POST['longitude_depart'].'</p>';

        echo '<p>lat arrivee: '.$_POST['latitude_arrivee'].'</p>';
        echo '<p>lng arrivee: '.$_POST['longitude_arrivee'].'</p>';

        echo $_POST['heure_prise_en_charge'];

		/*SELECT s.id as idSortie, s.*, n.id as idNiveau, n.*, c.id as idCategorie, c.*, u.id as idUtilisateur, u.* ,
			(
	            6366 * acos(
                    cos(
                        radians(".$_POST['latitude'].")
                        ) *
                    cos(
                    	radians(latitude)
                    ) *
                    cos(
                        radians(longitude) - radians(".$_POST['longitude'].")
                    ) +
                    sin(
                        radians(".$_POST['latitude'].")
                    ) *
                    sin(
                        radians(latitude)
                    )
                )
            ) AS distance
        FROM Sortie s
        INNER JOIN utilisateur u ON u.id = s.organisateur_id
    	INNER JOIN niveau n ON n.id = s.niveau_id
    	INNER JOIN categorie c ON c.id = s.categorie_id
    	WHERE 1
        AND s.is_publiee = 1
        AND s.date_de_sortie >= $dateNow

        HAVING distance < $_POST['rayon']

        ORDER BY s.date_de_sortie ASC*/

    	return $this->render('reservation/informations.html.twig', [
        	'infosReservation' => $_POST,
        	'prix' => $prix,
        	'prestation' => $prestation
        ]);
    }
  
  	/**
     * 
     *
     * @Route("/reservation/transport/email", name="reservation_transport_email")
     */
    public function reservation_transport_email(Request $request, Email $email)
    {
	 	if($_POST['forfait'] == "1 H"){
    		$_POST['prix'] = 65;
    	}elseif($_POST['forfait'] == "2 H"){
    		$_POST['prix'] = 120;
    	}elseif($_POST['forfait'] == "4 H"){
    		$_POST['prix'] = 240;
    	}elseif($_POST['forfait'] == "8 H"){
    		$_POST['prix'] = 400;
    	}elseif($_POST['forfait'] == "12 H"){
    		$_POST['prix'] = 600;
    	}elseif($_POST['forfait'] == "24 H"){
    		$_POST['prix'] = 1000;
    	}

        $courseRepository = $this->getDoctrine()->getRepository(Course::class);
        $course = $courseRepository->enregistre($_POST, "transport", $_POST['prix']);

        $reservationRepository = $this->getDoctrine()->getRepository(Reservation::class);
        $reservationRepository->enregistre($_POST, $course);

        $utilisateur = $this->getUser();
	  
    	//SWIFTMAILER
        $infosMail = [
		  	'prestation' => "transport",
            'civilite' => $_POST['civilite'],
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'adresse' => $utilisateur->getAdresse(),
            'tel' => $_POST['tel'],
            'email' => $_POST['email'],
            'date_prise_en_charge' => $_POST['date_prise_en_charge'],
            'heure_prise_en_charge' => $_POST['heure_prise_en_charge'],
            'adresse_depart' => $_POST['adresse_depart'],
            'adresse_arrivee' => $_POST['adresse_arrivee'],
            'forfait' => $_POST['forfait'],
		  	'prix' => $_POST['prix'],
            'nb_passagers' => $_POST['nb_passagers'],
            'nb_bagages' => $_POST['nb_bagages']
        ];
		
        $email->send($infosMail);

        return $this->forward('App\Controller\HelloController::Home', [
			'reservation' => true
	    ]); 
    }

    /**
     * @Route("/reservation/informations/transport", name="reservation_infos_transport")
     */
    public function infos_transport(Request $request)
    {
    	if($_POST['forfait'] == "1"){
            $prix = 65;
        }elseif($_POST['forfait'] == "2"){
            $prix = 120;
        }elseif($_POST['forfait'] == "4"){
            $prix = 240;
        }elseif($_POST['forfait'] == "8"){
            $prix = 400;
        }elseif($_POST['forfait'] == "12"){
            $prix = 600;
        }elseif($_POST['forfait'] == "24"){
            $prix = 1000;
        }

    	return $this->render('reservation/informations_transport.html.twig', [
        	'infosReservation' => $_POST,
        	'prix' => $prix
        ]);
    }

    /**
     * @Route("/reservation/informations/excursion/{id}", name="reservation_infos_excursion")
     */
    public function infos_excursion(Request $request, $id)
    {
    	$excursionRepository = $this->getDoctrine()->getRepository(Excursion::class);
        $excursion = $excursionRepository->find($id); 

    	return $this->render('reservation/informations_excursion.html.twig', [
        	'excursion' => $excursion,
        	'infosReservation' => $_POST
        ]);
    }
  
  	/**
     * 
     *
     * @Route("/reservation/excursion/{id}/email", name="reservation_excursion_email")
     */
    public function reservation_excursion_email(Request $request, Email $email, $id)
    {
    	$excursionRepository = $this->getDoctrine()->getRepository(Excursion::class);
        $excursion = $excursionRepository->find($id); 

        $courseRepository = $this->getDoctrine()->getRepository(Course::class);
        $course = $courseRepository->enregistre($_POST, "excursion", $excursion->getPrix());

        $reservationRepository = $this->getDoctrine()->getRepository(Reservation::class);
        $reservationRepository->enregistre($_POST, $course);

        $utilisateur = $this->getUser();

    	//SWIFTMAILER
        $infosMail = [
        	'prestation' => "excursion",
        	'excursion' => $excursion->getNom(),
            'civilite' => $_POST['civilite'],
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'adresse' => $utilisateur->getAdresse(),
            'tel' => $_POST['tel'],
            'email' => $_POST['email'],
            'date_prise_en_charge' => $_POST['date_prise_en_charge'],
            'heure_prise_en_charge' => $_POST['heure_prise_en_charge'],
            'adresse_depart' => $_POST['adresse_depart'],
            'adresse_arrivee' => $_POST['adresse_arrivee'],
			'prix' => $excursion->getPrix(),
            'nb_passagers' => $_POST['nb_passagers'],
            'nb_bagages' => $_POST['nb_bagages']
        ];

        $email->send($infosMail);

        return $this->forward('App\Controller\HelloController::Home', [
        	'reservation' => true
	    ]); 
    }

    /**
     * @Route("/reservation/informations/aventure/{idAventure}/mission/{idMission}", name="reservation_infos_aventure")
     */
    public function infos_aventure(Request $request, $idAventure, $idMission)
    {
    	$aventureRepository = $this->getDoctrine()->getRepository(Decouverte::class);
        $aventure = $aventureRepository->find($idAventure);

        $missionRepository = $this->getDoctrine()->getRepository(Mission::class);
        $mission = $missionRepository->find($idMission); 

    	return $this->render('reservation/informations_aventure.html.twig', [
        	'aventure' => $aventure,
        	'mission' => $mission,
        	'infosReservation' => $_POST
        ]);
    }
  
  	/**
     * 
     *
     * @Route("/reservation/aventure/{idAventure}/mission/{idMission}/email", name="reservation_aventure_email")
     */
    public function reservation_aventure_email(Request $request, Email $email, $idAventure, $idMission)
    {
    	$aventureRepository = $this->getDoctrine()->getRepository(Decouverte::class);
        $aventure = $aventureRepository->find($idAventure);

        $missionRepository = $this->getDoctrine()->getRepository(Mission::class);
        $mission = $missionRepository->find($idMission); 

        $courseRepository = $this->getDoctrine()->getRepository(Course::class);
        $course = $courseRepository->enregistre($_POST, "aventure", $mission->getPrix());

        $reservationRepository = $this->getDoctrine()->getRepository(Reservation::class);
        $reservationRepository->enregistre($_POST, $course);

        $utilisateur = $this->getUser();

    	//SWIFTMAILER
        $infosMail = [
        	'prestation' => "aventure",
        	'aventure' => $aventure->getNom(),
        	'mission' => $mission->getNom(),
            'civilite' => $_POST['civilite'],
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'adresse' => $utilisateur->getAdresse(),
            'tel' => $_POST['tel'],
            'email' => $_POST['email'],
            'date_prise_en_charge' => $_POST['date_prise_en_charge'],
            'heure_prise_en_charge' => $_POST['heure_prise_en_charge'],
            'adresse_depart' => $_POST['adresse_depart'],
            'adresse_arrivee' => $_POST['adresse_arrivee'],
			'prix' => $mission->getPrix(),
            'nb_passagers' => $_POST['nb_passagers'],
            'nb_bagages' => $_POST['nb_bagages']
        ];

        $email->send($infosMail);

        return $this->forward('App\Controller\HelloController::Home', [
        	'reservation' => true
	    ]); 
    }  

    /**
     * @Route("/reservation/informations", name="reservation_infos")
     */
    public function infos(Calcul $calcul, Request $request)
    {
		$centreZone1 = ['latitude' => 47.217935, 'longitude' => -1.543369];
		$centreZone2 = ['latitude' => 47.208015, 'longitude' => -1.552916];
		$centreAeroport = ['latitude' => 47.15741879999999, 'longitude' => -1.606231600000001];
		$rayonZone1 = 1.98;
		$rayonZone2 = 5.42;
		$rayonAeroport = 1;
		$zone = 0;
		$distanceParRoute = explode(" km", $_POST['distanceParRoute'])[0];
		$distanceParRoute = str_replace(",", ".", $distanceParRoute);
		$distanceParRoute = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $distanceParRoute);
		$distanceParRoute = floatval($distanceParRoute);

		$latLngAdresseDepart = [
			'latitude' => floatval($_POST['latitude_depart']),
			'longitude' => floatval($_POST['longitude_depart'])
		];

		$latLngAdresseArrivee = [
			'latitude' => floatval($_POST['latitude_arrivee']),
			'longitude' => floatval($_POST['longitude_arrivee'])
		];
		
		//DEPART
		$distanceDepartAcentreZone1 = $calcul->distancePoints($centreZone1['latitude'], $centreZone1['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));
		$distanceDepartAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));
		$distanceDepartAeroport = $calcul->distancePoints($centreAeroport['latitude'], $centreAeroport['longitude'], floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']));
		//ARRIVEE
		$distanceArriveeAcentreZone1 = $calcul->distancePoints($centreZone1['latitude'], $centreZone1['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));
		$distanceArriveeAcentreZone2 = $calcul->distancePoints($centreZone2['latitude'], $centreZone2['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));
		$distanceArriveeAeroport = $calcul->distancePoints($centreAeroport['latitude'], $centreAeroport['longitude'], floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));

		//DEPART
        $localiseDepart['cercle1'] = $calcul->adresseInCercle1($distanceDepartAcentreZone1, $rayonZone1);
		$localiseDepart['cercle2'] = $calcul->adresseInCercle2($distanceDepartAcentreZone2, $rayonZone2);
		//ARRIVEE
		$localiseArrivee['cercle1'] = $calcul->adresseInCercle1($distanceArriveeAcentreZone1, $rayonZone1);
		$localiseArrivee['cercle2'] = $calcul->adresseInCercle2($distanceArriveeAcentreZone2, $rayonZone2);

		//DEPART
		$localiseDepart['carre1Zone1'] = $calcul->adresseInCarre1Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre2Zone1'] = $calcul->adresseInCarre2Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre3Zone1'] = $calcul->adresseInCarre3Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre4Zone1'] = $calcul->adresseInCarre4Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre5Zone1'] = $calcul->adresseInCarre5Zone1($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);

		$localiseDepart['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);
		$localiseDepart['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseDepart['latitude'], $latLngAdresseDepart['longitude']);

		//ARRIVEE
		$localiseArrivee['carre1Zone1'] = $calcul->adresseInCarre1Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre2Zone1'] = $calcul->adresseInCarre2Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre3Zone1'] = $calcul->adresseInCarre3Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre4Zone1'] = $calcul->adresseInCarre4Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre5Zone1'] = $calcul->adresseInCarre5Zone1($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);

		$localiseArrivee['carre1Zone2'] = $calcul->adresseInCarre1Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre2Zone2'] = $calcul->adresseInCarre2Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre3Zone2'] = $calcul->adresseInCarre3Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre4Zone2'] = $calcul->adresseInCarre4Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);
		$localiseArrivee['carre5Zone2'] = $calcul->adresseInCarre5Zone2($latLngAdresseArrivee['latitude'], $latLngAdresseArrivee['longitude']);

		//DEPART
        $localiseDepart['aeroport'] = $calcul->adresseInAeroport($distanceDepartAeroport, $rayonAeroport);

		//ARRIVEE
        $localiseArrivee['aeroport'] = $calcul->adresseInAeroport($distanceArriveeAeroport, $rayonAeroport);

		//DEPART
		if(
			$localiseDepart['cercle1'] ||
			$localiseDepart['carre1Zone1'] ||
			$localiseDepart['carre2Zone1'] ||
			$localiseDepart['carre3Zone1'] ||
			$localiseDepart['carre4Zone1'] ||
			$localiseDepart['carre5Zone1']
		){
			$zoneDepart = "1";
		}elseif (
			$localiseDepart['cercle2'] ||
			$localiseDepart['carre1Zone2'] ||
			$localiseDepart['carre2Zone2'] ||
			$localiseDepart['carre3Zone2'] ||
			$localiseDepart['carre4Zone2'] ||
			$localiseDepart['carre5Zone2']
		){
			$zoneDepart = "2";
		}elseif($localiseDepart['aeroport']){
			$zoneDepart = "3"; // AEROPORT
		}else{
			$zoneDepart = "4"; // HORS ZONE
		}

		//ARRIVEE
		if(
			$localiseArrivee['cercle1'] ||
			$localiseArrivee['carre1Zone1'] ||
			$localiseArrivee['carre2Zone1'] ||
			$localiseArrivee['carre3Zone1'] ||
			$localiseArrivee['carre4Zone1'] ||
			$localiseArrivee['carre5Zone1']
		){
			$zoneArrivee = "1";
		}elseif (
			$localiseArrivee['cercle2'] ||
			$localiseArrivee['carre1Zone2'] ||
			$localiseArrivee['carre2Zone2'] ||
			$localiseArrivee['carre3Zone2'] ||
			$localiseArrivee['carre4Zone2'] ||
			$localiseArrivee['carre5Zone2']
		){
			$zoneArrivee = "2";
		}elseif($localiseArrivee['aeroport']){
			$zoneArrivee = "3"; // AEROPORT
		}else{
			$zoneArrivee = "4"; // HORS ZONE
		}
        
        echo '<p>Distance entre la zone 1 et le depart: '.($distanceDepartAcentreZone1).' Km</p>';
        echo '<p>Distance entre la zone 2 et le depart: '.($distanceDepartAcentreZone2).' Km</p>';

        echo '<p>Distance entre la zone 1 et larrivee: '.($distanceArriveeAcentreZone1).' Km</p>';
        echo '<p>Distance entre la zone 2 et larrivee: '.($distanceArriveeAcentreZone2).' Km</p>';

        echo '<p>Distance entre aeroport et le depart: '.($distanceDepartAeroport).' Km</p>';
        echo '<p>Distance entre aeroport et larrivee: '.($distanceArriveeAeroport).' Km</p>';

        echo '<p>Zone depart: '.$zoneDepart.'</p>';
        echo '<p>Zone arrivée: '.$zoneArrivee.'</p>';

        $distanceAdressesVol = $calcul->distancePoints(floatval($_POST['latitude_depart']), floatval($_POST['longitude_depart']), floatval($_POST['latitude_arrivee']), floatval($_POST['longitude_arrivee']));
        $prix = $calcul->prix($zoneDepart, $zoneArrivee, $distanceParRoute);

        echo '<p>Prix: '.$prix.'</p>';

        var_dump($localiseDepart);
        var_dump($localiseArrivee);

        echo '<p>Depart dans aeroport: '.$localiseDepart['aeroport'].'</p>';
        echo '<p>Arrivee dans aeroport: '.$localiseArrivee['aeroport'].'</p>';

        echo '<p>lat depart: '.$_POST['latitude_depart'].'</p>';
        echo '<p>lng depart: '.$_POST['longitude_depart'].'</p>';

        echo '<p>lat arrivee: '.$_POST['latitude_arrivee'].'</p>';
        echo '<p>lng arrivee: '.$_POST['longitude_arrivee'].'</p>';

        echo $_POST['heure_prise_en_charge'];

		/*SELECT s.id as idSortie, s.*, n.id as idNiveau, n.*, c.id as idCategorie, c.*, u.id as idUtilisateur, u.* ,
			(
	            6366 * acos(
                    cos(
                        radians(".$_POST['latitude'].")
                        ) *
                    cos(
                    	radians(latitude)
                    ) *
                    cos(
                        radians(longitude) - radians(".$_POST['longitude'].")
                    ) +
                    sin(
                        radians(".$_POST['latitude'].")
                    ) *
                    sin(
                        radians(latitude)
                    )
                )
            ) AS distance
        FROM Sortie s
        INNER JOIN utilisateur u ON u.id = s.organisateur_id
    	INNER JOIN niveau n ON n.id = s.niveau_id
    	INNER JOIN categorie c ON c.id = s.categorie_id
    	WHERE 1
        AND s.is_publiee = 1
        AND s.date_de_sortie >= $dateNow

        HAVING distance < $_POST['rayon']

        ORDER BY s.date_de_sortie ASC*/

        return $this->render('reservation/informations.html.twig', [
        	'infosReservation' => $_POST,
        	'prix' => $prix
        ]);
    }

    /*public function evenement(int $id)
    {
    	
		$evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
        $prestation = $evenementRepository->find($id);		

        $prestation->type = "evenement";

        return $this->render('reservation/index.html.twig', [
        	'prestation' => $prestation
        ]);
    }*/

    /**
     * @Route("/reservation/confirmer", name="reservation_confirmer")
     */
    public function confirmer()
    {
    	
		$reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);


        return $this->render('reservation/confirmation.html.twig', [

        ]);
    }
}
