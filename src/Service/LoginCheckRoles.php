<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Evenement;
use Twig\Environment;

/**
 * Class LoginCheckRoles
 *
 * @package App\AppListener
 */
class LoginCheckRoles implements AuthenticationSuccessHandlerInterface
{
    private $router;
    private $security;
    private $session;
    private $twig;

    /**
     * LoginCheckRoles constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, Security $security, SessionInterface $session, TokenStorageInterface $tokenStorage, EntityManagerInterface $em, Environment $twig)
    {
        $this->router = $router;
        $this->security = $security;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     *
     * @param TokenInterface $token
     *
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if($this->security->getUser()->getEtat()){

            $roles = $token->getRoles();

            $rolesTab = array_map(function ($role) {
                return $role->getRole();
            }, $roles);

            if (in_array('ROLE_ADMIN', $rolesTab, true)) {
                // c'est un aministrateur : on le rediriger vers l'espace admin
                $redirection = new RedirectResponse($this->router->generate('easyadmin'));
            } else {
                // c'est un utilisaeur lambda : on le rediriger vers l'accueil
                $redirection = new RedirectResponse($this->router->generate('accueil'));
            }

            return $redirection;
        }else{
            /*$this->session->getFlashBag()->add('success', 'Vous avez été banni!');*/
            $this->tokenStorage->setToken(null);
            $this->session->invalidate();

            /*$evenementRepository = $this->em->getRepository(Evenement::class);
            $nbDaysLimit = "30";
            $evenements = $evenementRepository->getNearEventsLimit3($nbDaysLimit);    

            $reservation = false;*/

            return new RedirectResponse($this->router->generate('accueil', [
                'etat' => false
            ]));
            
            /*return $this->twig->render('vtc/accueil.html.twig', [
                'evenements' => $evenements,
                'reservation' => $reservation
            ]);*/
        }
    }
}