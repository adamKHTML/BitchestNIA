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
use App\Repository\TransactionRepository; 

class SaleController extends AbstractController
{

    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }
    
    #[Route('/sale', name: 'app_sale')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cryptoRepository = $entityManager->getRepository(Crypto::class);
        $walletRepository = $entityManager->getRepository(Wallet::class);

        // Récupère l'utilisateur actuel
        $user = $this->getUser();

        // Récupère le portefeuille de l'utilisateur
        $wallet = $walletRepository->findOneBy(['user' => $user]);

        // Récupère les cryptomonnaies que l'utilisateur possède
        $userCryptos = $wallet->getCryptos();

        // Utilise le repository pour récupérer les informations complètes des cryptomonnaies
        $cryptos = $cryptoRepository->findById($userCryptos->getValues());

        if ($request->isMethod('POST')) {
            $cryptoId = $request->request->get('crypto');
            $amount = $request->request->get('amount');
        
            // Récupère la crypto sélectionnée
            $crypto = $cryptoRepository->find($cryptoId);
        
            // Vérifie si la quantité à vendre est valide
            if ($amount <= 0 || $amount > $wallet->getSoldeCryptos()) {
                $this->addFlash('danger', 'Montant invalide.');
                return $this->redirectToRoute('app_sale');
            }
        
            // Converti la chaîne de caractères en entier
            $amount = intval($amount);

           
        
            // Crée une transaction de vente
            $saleTransaction = new Transaction();
            $saleTransaction->setCrypto($crypto);
            $saleTransaction->setDate(new \DateTime());
            $saleTransaction->setQuantity($amount);
            $saleTransaction->setWallet($wallet); // Assurez-vous que cette ligne est correcte
            $saleTransaction->setType('sell');
            $entityManager->persist($saleTransaction);
        
            // Met à jour le solde du portefeuille après la vente
            $wallet->setSoldeEuro($wallet->getSoldeEuro() + ($crypto->getPrice() * $amount));
            $wallet->setSoldeCryptos($wallet->getSoldeCryptos() - $amount);
            $entityManager->persist($wallet);

            // Flush pour appliquer les changements dans la base de données
            $entityManager->flush();
        
             // Récupère toutes les transactions liées à la crypto sélectionnée
             $allTransactions = $this->transactionRepository->findAllTransactionsForCrypto($user, $crypto);

            // Vérifie si la quantité vendue est égale à la quantité initialement achetée
            $totalBought = 0;
            foreach ($allTransactions as $transaction) {
                if ($transaction->getType() == 'buy') {
                    $totalBought += $transaction->getQuantity();
                } elseif ($transaction->getType() == 'sell') {
                    $totalBought -= $transaction->getQuantity();
                }
            }
        
            // Supprime la relation entre la crypto et le portefeuille si la quantité vendue est égale à la quantité initialement achetée
            if ($totalBought == 0) {
                $wallet->removeCrypto($crypto);
                $entityManager->flush();
            }
        
            // Redirection de l'utilisateur vers la page de wallet ou affichez un message de confirmation
            $this->addFlash('success', 'Vente effectuée avec succès.');
            return $this->redirectToRoute('app_wallet');
        }

        return $this->render('sale/index.html.twig', [
            'cryptos' => $cryptos,
        ]);
    }
}