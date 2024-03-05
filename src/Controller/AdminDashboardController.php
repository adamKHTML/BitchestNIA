<?php


namespace App\Controller;

use App\Service\ChartService;
use App\Entity\Crypto;
use App\Repository\CryptoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(EntityManagerInterface $entityManager, ChartService $chartService): Response
    {
        $cryptoRepository = $entityManager->getRepository(Crypto::class);
        $cryptos = $cryptoRepository->findAll();

        $cryptoData = $chartService->generateChartData($cryptos);

        return $this->render('admin_dashboard/index.html.twig', [
            'cryptoData' => $cryptoData,
        ]);
    }
}