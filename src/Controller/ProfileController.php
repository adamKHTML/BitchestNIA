<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfileController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $isEditMode = $request->query->getBoolean('edit', false);

        // Créez un formulaire avec les champs nécessaires
        $form = $this->createFormBuilder($user)
            ->add('lastName')
            ->add('firstName')
            ->add('pseudo')
            ->add('email')
            ->add('password', PasswordType::class, ['mapped' => false]) // Ajoutez le champ de mot de passe avec 'mapped' => false
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez les modifications dans la base de données
            $this->entityManager->flush();

            // Rediriger vers la même page pour désactiver le mode édition
            return $this->redirectToRoute('app_profile', ['edit' => false]);
        }

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'form' => $form->createView(),
            'is_edit_mode' => $isEditMode,
        ]);
    }
}
