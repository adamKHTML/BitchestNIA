<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SaleController extends AbstractController
{
    #[Route('/sale', name: 'app_sale')]
    public function index(): Response
    {
        return $this->render('sale/index.html.twig', [
            'controller_name' => 'SaleController',
        ]);
    }
}
