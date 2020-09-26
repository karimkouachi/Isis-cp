<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use App\Entity\Evenement;
use App\Form\RegistrationType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
    	$utilisateur = new Utilisateur();
    	$form = $this->createForm(RegistrationType::class, $utilisateur);

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $utilisateur->setPassword(
                $encoder->encodePassword(
                    $utilisateur,
                    $form->get('password')->getData()
                )
            );

            /*$entityManager = $this->getDoctrine()->getManager();*/
            $manager->persist($utilisateur);
            $manager->flush();

            // do anything else you need here, like send an email

            /*return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );*/

            /*$evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
	        $nbDaysLimit = "30";
	        $evenements = $evenementRepository->getNearEventsLimit3($nbDaysLimit); 

	        $reservation = false;*/

            /*return $this->render('vtc/accueil.html.twig', [
	            'evenements' => $evenements,
	            'reservation' => $reservation
	        ]);*/

	        return $this->redirectToRoute('security_login');
        }       

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

    	return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){}
}
