<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\RouterInterface;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, RouterInterface $router): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // If the user is already logged in, redirect based on their role
        if ($this->getUser()) {
            $roles = array_map('strtoupper', $this->getUser()->getRoles());

            if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                // Redirection pour les utilisateurs ayant le rôle "ROLE_ADMIN"
                return $this->redirectToRoute('app_admin_dashboard');
            }elseif (in_array('ROLE_USER', $roles, true)) {
                // You can add a flash message here if needed
                return $this->redirectToRoute('app_client_dashboard');
            }
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}