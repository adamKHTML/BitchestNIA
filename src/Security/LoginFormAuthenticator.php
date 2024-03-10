<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use App\Entity\User; 
use Doctrine\ORM\EntityManagerInterface;




class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private AuthenticationUtils $authenticationUtils;
    private EntityManagerInterface $entityManager;

    

    public function __construct(UrlGeneratorInterface $urlGenerator, AuthenticationUtils $authenticationUtils,  EntityManagerInterface $entityManager)
    {
        $this->urlGenerator = $urlGenerator;
        $this->authenticationUtils = $authenticationUtils;
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
    
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);
       
        
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);
        
        // Vérifie si l'utilisateur est trouvé et s'il est banni
        if ($user && method_exists($user, 'getStatus') && $user->getStatus() === 'banned') {
           
            $exception = new AuthenticationException('You are banned.');
        
          
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        
            // Redirection l'utilisateur vers la page de connexion
            return new Passport(
                new UserBadge($email),
                new PasswordCredentials(''), 
                [
                    new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                    new RememberMeBadge(),
                ]
            );
        }
        
        // Vérifie si le champ du mot de passe est vide
        $password = $request->request->get('password', '');
        if (empty($password)) {
           
            $exception = new AuthenticationException('Invalid credentials.');
        
           
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        
            // Redirection l'utilisateur vers la page de connexion
            return new Passport(
                new UserBadge($email),
                new PasswordCredentials(''), 
                [
                    new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                    new RememberMeBadge(),
                ]
            );
        }
        
        // Si l'utilisateur n'est pas banni et que le champ du mot de passe n'est pas vide, chemin vers à l'authentification
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
        
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Récupère l'utilisateur authentifié
        $user = $token->getUser();

        if (method_exists($user, 'getRoles')) {
            $roles = $user->getRoles();

            // Vérifie si les rôles sont disponibles et redirection en conséquence
            if ($roles && is_array($roles)) {
                if (in_array('ROLE_ADMIN', $roles, true)) {
                    return new RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
                } elseif (in_array('ROLE_USER', $roles, true)) {
                    return new RedirectResponse($this->urlGenerator->generate('app_client_dashboard'));
                }
            }
        }

        // Si aucun rôle ni target path n'est disponible, redirection vers la page d'accueil par défaut
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}