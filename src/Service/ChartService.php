<?php 

namespace App\Service;

class ChartService
{
    public function generateChartData(array $cryptos): array
    {
        $cryptoData = [];

        foreach ($cryptos as $crypto) {
            // Génère les données pour les 6 premiers jours
            $randomData = $this->generateRandomData(6);

            // Ajoute le prix réel de la crypto pour le 7ème jour
            $realPrice = $crypto->getPrice(); 

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
           
            $randomData[] = mt_rand(10, 50); 
        }

        return $randomData;
    }
}