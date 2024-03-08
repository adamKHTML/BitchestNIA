<?php 

namespace App\Service;

class ChartService
{
    public function generateChartData(array $cryptos): array
    {
        $cryptoData = [];

        foreach ($cryptos as $crypto) {
            // Générer les données pour les 6 premiers jours
            $randomData = $this->generateRandomData(6);

            // Ajouter le prix réel de la crypto pour le 7ème jour
            $realPrice = $crypto->getPrice(); // Récupérer le prix réel depuis la base de données

            $cryptoData[$crypto->getName()] = [
                'labels' => ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                'data' => array_merge($randomData, [$realPrice]),
            ];
        }

        return $cryptoData;
    }

    private function generateRandomData($days): array
    {
        $randomData = [];
        for ($i = 0; $i < $days; $i++) {
            // Générez des données aléatoires pour les 6 premiers jours
            $randomData[] = mt_rand(10, 50); // Modifiez cette logique selon vos besoins
        }

        return $randomData;
    }
}