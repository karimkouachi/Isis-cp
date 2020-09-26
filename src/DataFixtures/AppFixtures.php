<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Evenement;
use App\Entity\Utilisateur;
use App\Entity\Decouverte;
use App\Entity\Mission;
use App\Entity\Excursion;

class AppFixtures extends Fixture
{
	private $encode;

	public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encode = $encoder;
    }  

    public function load(ObjectManager $manager)
    {
    	// UTILISATEUR ---------------------------------------------------------------------
    	$admin = new Utilisateur();
    	$admin->setCivilite("Monsieur");
    	$admin->setNom("Kouachi");
    	$admin->setPrenom("Djamel");
    	$admin->setEmail("Djamel@hotmail.fr");
    	$admin->setTelephone("070707070707");
    	$admin->setRoles(array("ROLE_ADMIN"));

    	$admin->setPassword(
            $this->encode->encodePassword(
                $admin,
                "ytreza44"
            )
        );

    	$manager->persist($admin);

    	$user = new Utilisateur();
    	$user->setCivilite("Monsieur");
    	$user->setNom("Kouachi");
    	$user->setPrenom("Karim");
    	$user->setEmail("Karim@hotmail.fr");
    	$user->setTelephone("070707070707");
    	$user->setRoles(array("ROLE_USER"));

    	$user->setPassword(
            $this->encode->encodePassword(
                $user,
                "ytreza44"
            )
        );

    	$manager->persist($user);

        $userBanni = new Utilisateur();
        $userBanni->setCivilite("Monsieur");
        $userBanni->setNom("Dupont");
        $userBanni->setPrenom("Jean");
        $userBanni->setEmail("Jean@hotmail.fr");
        $userBanni->setTelephone("070707070707");
        $userBanni->setRoles(array("ROLE_USER"));
        $userBanni->setEtat(false);

        $userBanni->setPassword(
            $this->encode->encodePassword(
                $userBanni,
                "ytreza44"
            )
        );

        $manager->persist($userBanni);

    	// EVENEMENT ---------------------------------------------------------------------
    	$evenement1 = new Evenement();
    	$evenement1->setNom("Evenement 1");
    	$evenement1->setGenre("Concert");
        $evenement1->setAdresse("Zénith Nantes Métropole, Boulevard du Zénith, Saint-Herblain, France");
        $evenement1->setLatitude(47.2287056);
        $evenement1->setLongitude(-1.6279365999999982);
    	$evenement1->setDateDebut(new \DateTime('2020/02/01'));
    	$evenement1->setImage("concert.jpeg");

    	$manager->persist($evenement1);

    	$evenement2 = new Evenement();
    	$evenement2->setNom("Evenement 2");
    	$evenement2->setGenre("Spectacle");
        $evenement2->setAdresse("Cité des Congrès de Nantes, Nantes, France");
        $evenement2->setLatitude(47.2129634);
        $evenement2->setLongitude(-1.5426443000000063);
    	$evenement2->setDateDebut(new \DateTime('2020/02/01'));
    	$evenement2->setImage("spectacle.jpg");

    	$manager->persist($evenement2);

    	$evenement3 = new Evenement();
    	$evenement3->setNom("Evenement 3");
    	$evenement3->setGenre("Sport");
        $evenement3->setAdresse("Stade de la Beaujoire, Nantes, France");
        $evenement3->setLatitude(47.2560232);
        $evenement3->setLongitude(-1.5246749000000364);
    	$evenement3->setDateDebut(new \DateTime('2020/02/01'));
    	$evenement3->setImage("sport.jpg");

    	$manager->persist($evenement3);

    	$evenement4 = new Evenement();
    	$evenement4->setNom("Evenement 4");
    	$evenement4->setGenre("Concert");
        $evenement4->setAdresse("Zénith Nantes Métropole, Boulevard du Zénith, Saint-Herblain, France");
        $evenement4->setLatitude(47.2287056);
        $evenement4->setLongitude(-1.6279365999999982);
    	$evenement4->setDateDebut(new \DateTime('2020/02/01'));
    	$evenement4->setImage("concert.jpeg");

    	$manager->persist($evenement4);

    	$evenement5 = new Evenement();
    	$evenement5->setNom("Evenement 5");
    	$evenement5->setGenre("Concert");
        $evenement5->setAdresse("Zénith Nantes Métropole, Boulevard du Zénith, Saint-Herblain, France");
        $evenement5->setLatitude(47.2287056);
        $evenement5->setLongitude(-1.6279365999999982);
    	$evenement5->setDateDebut(new \DateTime('2020/02/01'));
    	$evenement5->setImage("concert.jpeg");

    	$manager->persist($evenement5);

    	$evenement6 = new Evenement();
    	$evenement6->setNom("Evenement 6");
    	$evenement6->setGenre("Spectacle");
        $evenement6->setAdresse("Cité des Congrès de Nantes, Nantes, France");
        $evenement6->setLatitude(47.2129634);
        $evenement6->setLongitude(-1.5426443000000063);
    	$evenement6->setDateDebut(new \DateTime('2020/02/01'));
    	$evenement6->setImage("spectacle.jpg");

    	$manager->persist($evenement6);

    	$evenement7 = new Evenement();
    	$evenement7->setNom("Evenement 7");
    	$evenement7->setGenre("Sport");
        $evenement7->setAdresse("Stade de la Beaujoire, Nantes, France");
        $evenement7->setLatitude(47.2560232);
        $evenement7->setLongitude(-1.5246749000000364);
    	$evenement7->setDateDebut(new \DateTime('2020/02/01'));
    	$evenement7->setImage("sport.jpg");

    	$manager->persist($evenement7);

    	// DECOUVERTE ---------------------------------------------------------------------
    	$decouverte1 = new Decouverte();
    	$decouverte1->setNom("Voltige");
    	$decouverte1->setDescription("Tour en avion");
    	$decouverte1->setImage("voltige.jpg");

    	$manager->persist($decouverte1);

        $decouverte2 = new Decouverte();
        $decouverte2->setNom("Montgolfière");
        $decouverte2->setDescription("Tour en montgolfière");
        $decouverte2->setImage("montgolfiere.jpg");

        $manager->persist($decouverte2);

        $decouverte3 = new Decouverte();
        $decouverte3->setNom("Parachutisme");
        $decouverte3->setDescription("Tour en parachute");
        $decouverte3->setImage("parachutisme.jpg");

        $manager->persist($decouverte3);

        // MISSION ---------------------------------------------------------------------
        $mission = new Mission();
        $mission->setNom("Mission reconnaissance");
        $mission->setAdresse("Base ULM et de loisirs des Fontenelles en Vendée, Le Bernard, Pays de la Loire, France");
        $mission->setLatitude(46,446);
        $mission->setLongitude(-1,443);
        $mission->setDescription("VOUS DECOUVRIREZ LES FAMEUX « G » EN REALISANT LES GRANDS CLASSIQUES DE LA VOLTIGE AERIENNE : LOOPING, TONNEAU, RENVERSEMENT… UNE BELLE OCCASION DE VOLER SUR LE DOS ! C’EST LE PROGRAMME IDEAL POUR DECOUVRIR LA VOLTIGE AERIENNE, EN DOUCEUR.");
        $mission->setPrix(499.00);
        $mission->setImage("reconnaissance.jpeg");
        $mission->setDecouverte($decouverte1);

        $manager->persist($mission);

        $mission = new Mission();
        $mission->setNom("Mission combat");
        $mission->setAdresse("Base ULM et de loisirs des Fontenelles en Vendée, Le Bernard, Pays de la Loire, France");
        $mission->setLatitude(46,446);
        $mission->setLongitude(-1,443);
        $mission->setDescription("VOUS ETES DEJA AMATEUR DE SENSATIONS FORTES, ET VOUS CRAIGNEZ DE VOUS ENNUYER AVEC LA MISSION « RECO » ? PAS DE PROBLEME, ON A MIEUX AVEC LA MISSION « COMBAT » ! VOUS IREZ PLUS LOIN DANS LES FACTEURS DE CHARGE, ET GOUTEREZ AUX FIGURES DE COMPETITION, TELLES QUE L’AVALANCHE, LA CLOCHE ET LES TONNEAUX A FACETTES.");
        $mission->setPrix(499.00);
        $mission->setImage("combat.jpg");
        $mission->setDecouverte($decouverte1);

        $manager->persist($mission);

        $mission = new Mission();
        $mission->setNom("Mission extrème");
        $mission->setAdresse("Base ULM et de loisirs des Fontenelles en Vendée, Le Bernard, Pays de la Loire, France");
        $mission->setLatitude(46,446);
        $mission->setLongitude(-1,443);
        $mission->setDescription("VOUS VOULEZ DECOUVRIR LES SENSATIONS LES PLUS FORTES QUE PEUT PROCURER LA VOLTIGE AERIENNE ? LA MISSION « EXTREME » EST FAITE POUR VOUS ! CETTE MISSION RESTE ACCESSIBLE A TOUS, MEME SANS AVOIR DEJA FAIT DE VOLTIGE, A CONDITION D’ETRE UN GROS GOURMAND EN ADRENALINE…");
        $mission->setPrix(599.00);
        $mission->setImage("extreme.jpg");
        $mission->setDecouverte($decouverte1);

        $manager->persist($mission);

        $mission = new Mission();
        $mission->setNom("Vol exclusif");
        $mission->setAdresse("Base ULM et de loisirs des Fontenelles en Vendée, Le Bernard, Pays de la Loire, France");
        $mission->setLatitude(46,446);
        $mission->setLongitude(-1,443);
        $mission->setDescription("A BORD DE VOTRE NACELLE PRIVATISEE. DEVENEZ ACTEUR DE VOTRE VOL ET PARTICIPER A LA MISE EN PLACE AVEC L’EQUIPAGE. PARTEZ POUR 3 HEURES D’AVENTURE DONT UNE HEURE DE VOL, AU LEVER OU AU COUCHER DU SOLEIL.");
        $mission->setPrix(499.00);
        $mission->setImage("exclusif.jpg");
        $mission->setDecouverte($decouverte2);

        $manager->persist($mission);

        $mission = new Mission();
        $mission->setNom("Vol soleil levant");
        $mission->setAdresse("Base ULM et de loisirs des Fontenelles en Vendée, Le Bernard, Pays de la Loire, France");
        $mission->setLatitude(46,446);
        $mission->setLongitude(-1,443);
        $mission->setDescription("DECOLLEZ DES L’AUBE, LE SOLEIL ARIVERA ! A BORD D’UNE NACELLE JUSQU’A 6 PASSAGERS. 1H30 DE VOL POUR 4 HEURES D’EXCURSIONS. VALABLE UNIQUEMENT LE MATIN.");
        $mission->setPrix(549.00);
        $mission->setImage("soleil.jpg");
        $mission->setDecouverte($decouverte2);

        $manager->persist($mission);

        $mission = new Mission();
        $mission->setNom("La baie de saint nazaire");
        $mission->setAdresse("Base ULM et de loisirs des Fontenelles en Vendée, Le Bernard, Pays de la Loire, France");
        $mission->setLatitude(46,446);
        $mission->setLongitude(-1,443);
        $mission->setDescription("UN POINT DE VUE MAGNIFIQUE S’ETENDANT DU GOLFE DU MORBILLAN A NOIRMOUTIER.");
        $mission->setPrix(399.00);
        $mission->setImage("baie.jpg");
        $mission->setDecouverte($decouverte3);

        $manager->persist($mission);

        $mission = new Mission();
        $mission->setNom("La côte vendéenne");
        $mission->setAdresse("Base ULM et de loisirs des Fontenelles en Vendée, Le Bernard, Pays de la Loire, France");
        $mission->setLatitude(46,446);
        $mission->setLongitude(-1,443);
        $mission->setDescription("PROFITEZ D’UN MAGNIFIQUE PANORAMA SUR L’ILE D’YEU, L’ILE DE RE, ET L’ILE D’OLERON.");
        $mission->setPrix(499.00);
        $mission->setImage("vendee.jpg");
        $mission->setDecouverte($decouverte3);

        $manager->persist($mission);

    	// EXCURSION ---------------------------------------------------------------------
    	$excursion = new Excursion();
    	$excursion->setNom("Côte Atlantique");
    	$excursion->setDescription("Description 1");
    	$excursion->setImage("atlantique.jpg");

    	$manager->persist($excursion);

    	$manager->flush();
        
        
    }
}
