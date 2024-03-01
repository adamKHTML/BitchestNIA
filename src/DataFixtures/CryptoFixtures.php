<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Crypto;

class CryptoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $cryptoNames = [
            'Bitcoin', 'Ethereum', 'Ripple', 'Bitcoin Cash', 'Cardano',
            'Litecoin', 'NEM', 'Stellar', 'IOTA', 'Dash'
        ];

        foreach ($cryptoNames as $cryptoName) {
            $crypto = new Crypto();
            $crypto->setName($cryptoName);
            // Générez une variation aléatoire de pourcentage entre -60 et 60
            $percentageChange = mt_rand(-600, 600) / 10;  // multiplication par 10 pour obtenir une variation entre -60 et 60

            // Définissez la valeur initiale en ajustant la variation de pourcentage
            $initialValue = mt_rand(-20, 45) * (1 + ($percentageChange / 100));

            $crypto->setActualValue($initialValue);

            $manager->persist($crypto);
        }

        $manager->flush();
    }
}
