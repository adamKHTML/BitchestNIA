<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Repository\CryptoRepository;
use App\Service\ChartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ClientDashboardController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function index(CryptoRepository $cryptoRepository, ChartService $chartService): Response
    {
        $user = $this->getUser();
        $wallets = $user->getWallets();

        $soldeEuro = null;
        if (!$wallets->isEmpty()) {
            $soldeEuro = $wallets->first()->getSoldeEuro();
        }

        $cryptos = $cryptoRepository->findAll();

        // Génération des données pour le graphique
        $cryptoDataSets = [];
        foreach ($cryptos as $crypto) {
            // Génére les données pour les 6 premiers jours
            $randomData = $this->generateRandomData(6);

            // Ajout du prix réel de la crypto pour le 7ème jour
            $realPrice = $crypto->getPrice(); // Récupére le prix réel depuis la base de données

            $cryptoDataSets[$crypto->getName()] = [
                'labels' => ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                'data' => array_merge($randomData, [$realPrice]),
            ];
        }

        return $this->render('client_dashboard/index.html.twig', [
            'controller_name' => 'ClientDashboardController',
            'soldeEuro' => $soldeEuro,
            'cryptos' => $cryptos,
            'cryptoData' => $cryptoDataSets,
        ]);
    }

    private function generateRandomData($days): array
    {
        $randomData = [];
        for ($i = 0; $i < $days; $i++) {
            // Génére des données aléatoires pour les 6 premiers jours
            $randomData[] = mt_rand(10, 50); 
        }

        return $randomData;
    }
}