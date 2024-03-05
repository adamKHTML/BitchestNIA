<?php

namespace App\Controller;

use App\Entity\Crypto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class CryptoManagementController extends AbstractController
{
    #[Route('/crypto/management', name: 'app_crypto_management')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $cryptoRepository = $entityManager->getRepository(Crypto::class);
        $cryptos = $cryptoRepository->findAll();

        return $this->render('crypto_management/index.html.twig', [
            'cryptos' => $cryptos,
        ]);
    }
}
