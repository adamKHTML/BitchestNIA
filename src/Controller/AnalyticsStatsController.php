<?php



namespace App\Controller;

use App\Entity\User;
use App\Entity\Crypto;
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class AnalyticsStatsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/analytics-stats', name: 'app_analytics_stats')]
    public function index(): Response
    {
        $totalUsers = $this->getTotalUsers();

       

        return $this->render('analytics_stats/index.html.twig', [
            'totalUsers' => $totalUsers,
            
        ]);
    }

    // Méthode pour obtenir le nombre total d'utilisateurs
    private function getTotalUsers(): int
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $totalUsers = count($userRepository->findAll());

        return $totalUsers;
    }

    #[Route('/admin/analytics-stats', name: 'app_analytics_stats')]
public function analyticsStats(): Response
{
    $totalUsers = $this->entityManager->getRepository(User::class)->count([]);
    $cryptoData = $this->getTopCryptosData();

    return $this->render('analytics_stats/index.html.twig', [
        'totalUsers' => $totalUsers,
        'cryptoData' => $cryptoData,
    ]);
}

    private function getTopCryptosData(): array
    {
        $cryptoRepository = $this->entityManager->getRepository(Crypto::class);
        $cryptos = $cryptoRepository->findAll();
    
        $cryptoData = [
            'labels' => [],
            'data' => [],
        ];
    
        foreach ($cryptos as $crypto) {
            $totalQuantity = $this->getTotalQuantityForCrypto($crypto);
            $cryptoData['labels'][] = $crypto->getName();
            $cryptoData['data'][] = $totalQuantity;
        }
    
        return $cryptoData;
    }
    
    private function getTotalQuantityForCrypto(Crypto $crypto): int
    {
        $cryptoTransactions = $this->entityManager->getRepository(Transaction::class)
            ->findBy(['crypto' => $crypto]);
    
        $totalQuantity = 0;
        foreach ($cryptoTransactions as $transaction) {
            $totalQuantity += $transaction->getQuantity();
        }
    
        return $totalQuantity;
    }
}
