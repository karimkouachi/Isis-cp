<?php

// src/Controller/HelloController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Evenement;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Entity\Decouverte;
use App\Service\Email;
use App\Form\RegistrationType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HelloController extends AbstractController
{
    /**
     * Page d'accueil
     *
     * @Route("/", name="accueil")
     */
    public function home(/*UserPasswordEncoderInterface $encoder*/ Request $request, $reservation = 0, $contact = 0, $etat = 1)
    {

        if($request->query->has('etat')){
            $etat = 0;
        }else{
            $etat = 1;
        }

        $evenementRepository = $this->getDoctrine()->getRepository(Evenement::class);
        $nbDaysLimit = "30";
        $evenements = $evenementRepository->getNearEventsLimit3($nbDaysLimit);    

        return $this->render('vtc/accueil.html.twig', [
            'evenements' => $evenements,
            'reservation' => $reservation,
            'contact' => $contact,
            'etat' => $etat
        ]);
    }

    /**
     *
     *
     * @Route("/apropos", name="apropos")
     */
    public function apropos()
    {
        return $this->render('vtc/apropos.html.twig', [

        ]);
    }

    /**
     *
     *
     * @Route("/flotte", name="flotte")
     */
    public function flotte()
    {
        return $this->render('vtc/la_flotte.html.twig', [

        ]);
    }

    /**
     *
     *
     * @Route("/mentions legales", name="mentions_legales")
     */
    public function mentions_legales()
    {
        return $this->render('vtc/mentions_legales.html.twig', [

        ]);
    }

    /**
     *
     *
     * @Route("/cgv cgu", name="cgv_cgu")
     */
    public function cgv_cgu()
    {
        return $this->render('vtc/cgv_cgu.html.twig', [

        ]);
    }
  
  	/**
     *
     *
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $utilisateurRepository = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateur = $utilisateurRepository->find($this->getUser());

        $form = $this->createForm(RegistrationType::class, $utilisateur);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur = $form->getData();
            $utilisateur->setPassword(
                $encoder->encodePassword(
                    $utilisateur,
                    $form->get('password')->getData()
                )
            );
            $em->persist($utilisateur);
            $em->flush();
        }

        return $this->render('vtc/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     *
     *
     * @Route("/contact", name="contact")
     */
    public function contact(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('vtc/contact.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     *
     *
     * @Route("/tarifs", name="tarifs")
     */
    public function tarifs()
    {
        return $this->render('tarif/tarifs.html.twig', [

        ]);
    }

    /**
     *
     *
     * @Route("/tarif/metropole", name="tarif_metropole")
     */
    public function tarif_metropole()
    {
        return $this->render('tarif/tarif_metropole.html.twig', [

        ]);
    }

    /**
     *
     *
     * @Route("/tarif/departemental", name="tarif_departemental")
     */
    public function tarif_departemental()
    {
        return $this->render('tarif/tarif_departemental.html.twig', [

        ]);
    }

    /**
     *
     *
     * @Route("/tarif/regional", name="tarif_regional")
     */
    public function tarif_regional()
    {
        return $this->render('tarif/tarif_regional.html.twig', [

        ]);
    }

    /**
     *
     *
     * @Route("/tarif/sud-loire", name="tarif_sud_loire")
     */
    public function tarif_sud_loire()
    {
        return $this->render('tarif/tarif_sud_loire.html.twig', [

        ]);
    }

    /**
     *
     *
     * @Route("/tarif/sur_mesure", name="tarif_sur_mesure")
     */
    public function tarif_sur_mesure()
    {
        return $this->render('tarif/tarif_sur_mesure.html.twig', [

        ]);
    }
  
  	/**
     *
     *
     * @Route("/contact/email", name="vtc_email_contact")
     */
    public function contact_email(\Swift_Mailer $mailer, Request $request, Email $email)
    {
        $infosMail = [
            'Civilite' => $_POST['civilite']/*$this->getUser()->getCivilite()*/,
            'Nom' => $_POST['nom']/*$this->getUser()->getNom()*/,
            'Prenom' => $_POST['prenom']/*$this->getUser()->getPrenom()*/,
            'Tel' => $_POST['tel']/*$this->getUser()->getTelephone()*/,
            'Email' => $_POST['email'],
            'Message' => $_POST['message']
        ];

        $email->send_contact($infosMail);

        return $this->forward('App\Controller\HelloController::Home', [
            'contact' => true
        ]); 
    }

    /**
     *
     *
     * @Route("/email", name="email")
     */
    public function email(\Swift_Mailer $mailer, Request $request)
    {
        return $this->forward('App\Controller\HelloController::Home', [
            'reservation' => true
        ]);
    }
}