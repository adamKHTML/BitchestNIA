<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Transaction; // Ajout de l'import
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AllTransactionsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/all_transactions', name: 'app_all_transactions')]
    public function index(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $userTransactions = [];
        foreach ($users as $user) {
            $wallets = $user->getWallets();
            $transactions = [];
            
            foreach ($wallets as $wallet) {
                // Utilisez la relation définie dans l'entité Transaction
                $walletTransactions = $this->entityManager->getRepository(Transaction::class)
                    ->findBy(['wallet' => $wallet]);
                
                $transactions = array_merge($transactions, $walletTransactions);
            }

            $userTransactions[] = [
                'user' => $user,
                'transactions' => $transactions,
            ];
        }

        return $this->render('all_transactions/index.html.twig', [
            'userTransactions' => $userTransactions,
        ]);
    }
}