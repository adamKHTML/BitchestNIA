<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Repository\CryptoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ClientDashboardController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function index(CryptoRepository $cryptoRepository): Response
    {
        $user = $this->getUser();
        $wallets = $user->getWallets();

        $soldeEuro = null;
        if (!$wallets->isEmpty()) {
            $soldeEuro = $wallets->first()->getSoldeEuro();
        }

        $cryptos = $cryptoRepository->findAll();

        return $this->render('client_dashboard/index.html.twig', [
            'controller_name' => 'ClientDashboardController',
            'soldeEuro' => $soldeEuro,
            'cryptos' => $cryptos,
        ]);
    }


    #[Route('/client/dashboard', name: 'client_dashboard')]
    public function dashboard(CryptoRepository $cryptoRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $cryptos = $cryptoRepository->findAll();
    
        foreach ($cryptos as $crypto) {
            // Vérifiez si les valeurs ont déjà été générées
            if ($crypto->getPrice() === null) {
                // Génération aléatoire des valeurs pour price, priceBTC et actualValue
                $randomPrice = mt_rand(50, 150); // Valeur aléatoire entre 50 et 150
                $randomPriceBTC = $randomPrice / mt_rand(1000, 1500); // Conversion arbitraire en BTC
                $randomActualValue = mt_rand(80, 120); // Valeur aléatoire entre 80 et 120
    
                // Mettez à jour les propriétés dans l'entité Crypto
                $crypto->setPrice($randomPrice);
                $crypto->setPriceBTC($randomPriceBTC);
                $crypto->setActualValue($randomActualValue);
    
                // Enregistrez les modifications dans la base de données
                $entityManager->persist($crypto);
                $entityManager->flush();
            }
        }
    
        $cryptoDataSets = [];
        foreach ($cryptos as $crypto) {
            $cryptoValues = $this->generateCryptoValues($crypto);
            $cryptoDataSets[] = [
                'id' => $crypto->getId(),
                'name' => $crypto->getName(),
                'actual_value' => $crypto->getActualValue(),
                'price' => $crypto->getPrice(),
                'price_btc' => $crypto->getPriceBTC(),
                'cryptoValues' => $cryptoValues,
            ];
        }
    
        // Utilisez JsonResponse pour envoyer les données en tant que JSON
        return new JsonResponse(['cryptos' => $cryptoDataSets]);
    }
    private function generateCryptoValues(Crypto $crypto): array
    {
        $session = $this->get('session');

        // Utilisez une clé unique pour chaque crypto-monnaie
        $sessionKey = 'crypto_values_' . $crypto->getId();

        // Vérifiez si les valeurs ont déjà été générées
        if ($session->has($sessionKey)) {
            return $session->get($sessionKey);
        }

        // Si les valeurs n'ont pas encore été générées, générez-les
        $cotations = $crypto->getCotations();
        usort($cotations, function ($a, $b) {
            return $a->getDate() <=> $b->getDate();
        });

        $cryptoValues = [];
        $currentValue = $crypto->getActualValue();

        foreach ($cotations as $cotation) {
            // Mettez à jour l'actual_value de la crypto
            $currentValue = $cotation->getActualValue();
            $cryptoValues[] = [
                'x' => $cotation->getDate()->getTimestamp() * 1000,
                'y' => $currentValue,
            ];
        }

        // Enregistrez les valeurs dans la session
        $session->set($sessionKey, $cryptoValues);

        return $cryptoValues;
    }

    private function getRandomColor(): string
    {
        return '#' . substr(md5(rand()), 0, 6);
    }
}
