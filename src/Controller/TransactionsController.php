<?php

namespace App\Controller;

use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class TransactionsController extends AbstractController
{
    #[Route('/transactions', name: 'app_transactions')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transactionRepository = $entityManager->getRepository(Transaction::class);
        $transactions = $transactionRepository->findAll();

        return $this->render('transactions/index.html.twig', [
            'transactions' => $transactions,
        ]);
    }
}
