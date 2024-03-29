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
       
        $error = $authenticationUtils->getLastAuthenticationError();

        // Gestion d'erreur si le compte est banni
        if ($this->isBannedError($error)) {
            $error = 'Sorry, your account has been banned.';
        }

        
        $lastUsername = $authenticationUtils->getLastUsername();
         
       

      
        if ($this->getUser()) {
            $roles = array_map('strtoupper', $this->getUser()->getRoles());

            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('app_admin_dashboard');
            } elseif (in_array('ROLE_USER', $roles)) {
                return $this->redirectToRoute('app_client_dashboard');
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    private function isBannedError($error): bool
    {
        return method_exists($error, 'getMessage') && $error->getMessage() === 'You are banned.';
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
