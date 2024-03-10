<?php

namespace App\Controller;

use App\Repository\CryptoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WalletController extends AbstractController
{
    private $entityManager;
    private $cryptoRepository;

    public function __construct(EntityManagerInterface $entityManager, CryptoRepository $cryptoRepository)
    {
        $this->entityManager = $entityManager;
        $this->cryptoRepository = $cryptoRepository;
    }

    #[Route('/wallet', name: 'app_wallet')]
    public function index(Request $request): Response
    {
        // Récupère les portefeuilles de l'utilisateur
        $wallets = $this->getUser()->getWallets();

        // Vérifie si le formulaire a été soumis
        if ($request->isMethod('POST')) {
            // Met à jour le nom du portefeuille
            $newWalletName = $request->request->get('new_wallet_name');
            
            if (!empty($newWalletName)) {
                foreach ($wallets as $wallet) {
                    $wallet->setName($newWalletName);
                }

                $this->entityManager->flush();
            }
        }

        return $this->render('wallet/index.html.twig', [
            'wallets' => $wallets,
        ]);
    }
}
