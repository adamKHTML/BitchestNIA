<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Entity\Transaction;
use App\Entity\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PayController extends AbstractController
{
    #[Route('/pay', name: 'app_pay')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cryptoRepository = $entityManager->getRepository(Crypto::class);
        $walletRepository = $entityManager->getRepository(Wallet::class);

        $cryptos = $cryptoRepository->findAll();

        // Gère le formulaire d'achat
        if ($request->isMethod('POST')) {
            $cryptoId = $request->request->get('crypto');
            $amount = $request->request->get('amount');

            // Récupére la crypto sélectionnée
            $crypto = $cryptoRepository->find($cryptoId);

            // Récupére l'utilisateur actuel
            $user = $this->getUser();

            // Récupére le seul wallet de l'utilisateur 
            $wallet = $user->getWallets()->first();

            // Créer une transaction
            $transaction = new Transaction();
            $transaction->setCrypto($crypto);
            $transaction->setDate(new \DateTime());
            $transaction->setQuantity($amount);
            $transaction->setWallet($wallet);
            $transaction->setType('buy'); // Vous pouvez ajuster selon vos besoins
            $entityManager->persist($transaction);

            // Met à jour le solde du portefeuille
            $wallet->setSoldeEuro($wallet->getSoldeEuro() - ($crypto->getPrice() * $amount));
            $wallet->setSoldeCryptos($wallet->getSoldeCryptos() + $amount);
            $entityManager->persist($wallet);

            // Ajout d'une entrée dans la table crypto_wallet
            $wallet->addCrypto($crypto);
            $entityManager->persist($wallet);

            $entityManager->flush();

            // Redirige l'utilisateur vers la page de wallet ou affichez un message de confirmation
            return $this->redirectToRoute('app_wallet');
        }

        return $this->render('pay/index.html.twig', [
            'cryptos' => $cryptos,
        ]);
    }
}