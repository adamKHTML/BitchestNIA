<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProfileController extends AbstractController
{
    private $entityManager; 
 
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/profile', name: 'app_admin_profile')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $isEditMode = $request->query->getBoolean('edit', false);

      
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->flush();

          
            return $this->redirectToRoute('app_admin_profile', ['edit' => false]);
        }

        return $this->render('admin_profile/index.html.twig', [
            'controller_name' => 'AdminProfileController',
            'form' => $form->createView(),
            'is_edit_mode' => $isEditMode,
        ]);
    }
}
