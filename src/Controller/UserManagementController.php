<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class UserManagementController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/user-management', name: 'app_user_management')]
    public function index(): Response
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('user_management/index.html.twig', [
            'users' => $users,
        ]);
    }
    
    #[Route('admin/user/{id}/delete', name: 'app_user_delete')]
    public function deleteUser(User $user): Response
    {
        try {
            $this->entityManager->beginTransaction();

            // Générer un texte des transactions avant de les supprimer
            $transactionText = $this->generateTransactionText($user);

            
          

            // Enregistrez le texte des transactions liées à ce portefeuille
            // (vous pouvez le stocker, le journaliser, etc.)
            $this->saveTransactionText($transactionText);

            // Logique
            $transactions = $this->entityManager->getRepository(Transaction::class)
                ->createQueryBuilder('t')
                ->join('t.wallet', 'w')
                ->where('w.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();

            foreach ($transactions as $transaction) {
                // Supprimer la transaction via l'EntityManager
                $this->entityManager->remove($transaction);

                // Supprimer le portefeuille associé à la transaction
                $wallet = $transaction->getWallet();
                $this->entityManager->remove($wallet);
            }

            // Supprimer l'utilisateur après avoir traité les transactions et les portefeuilles
            $this->entityManager->remove($user);

            // ...

            $this->entityManager->flush();
            $this->entityManager->commit();

            $this->addFlash('success', 'Utilisateur, portefeuilles et transactions associées supprimés avec succès.');
        } catch (\Exception $exception) {
            $this->entityManager->rollback();

          

            $this->addFlash('error', 'Une erreur s\'est produite lors de la suppression de l\'utilisateur, des portefeuilles et des transactions associées.');
        }

        return $this->redirectToRoute('app_user_management');
    }
    #[Route('/admin/user/{id}/change-status', name: 'app_user_change_status')]
    public function changeUserStatus(User $user): Response
    {
        // Logique pour changer le statut de l'utilisateur (basculer entre 'actif' et 'banni', par exemple)
        $newStatus = ($user->getStatus() === 'actif') ? 'banni' : 'actif';
        $user->setStatus($newStatus);
        $this->entityManager->flush();

        $this->addFlash('success', 'Statut de l\'utilisateur modifié avec succès.');

        return $this->redirectToRoute('app_user_management');
    }

    #[Route('/admin/user/{id}/change-roles', name: 'app_user_change_roles')]
    public function changeUserRoles(User $user): Response
    {
        // Logique pour changer les rôles de l'utilisateur (basculer entre 'ROLE_USER' et 'ROLE_ADMIN', par exemple)
        $user->changeRoles(); // Supposez que vous ayez une méthode comme celle-ci dans votre entité User
        $this->entityManager->flush();

        $this->addFlash('success', 'Rôles de l\'utilisateur modifiés avec succès.');

        return $this->redirectToRoute('app_user_management');
    }

    private function generateTransactionText(User $user): string
{
    $transactionText = '';

    $transactions = $this->entityManager->getRepository(Transaction::class)
        ->createQueryBuilder('t')
        ->join('t.wallet', 'w')
        ->join('w.user', 'u')
        ->where('u.id = :userId')
        ->setParameter('userId', $user->getId())
        ->getQuery()
        ->getResult();

    foreach ($transactions as $transaction) {
        // Utilisez $transaction->getDate(), $transaction->getPrice(), etc. pour accéder aux valeurs
        $transactionText .= $this->formatTransactionTextForTransaction($transaction);
    }

    return $transactionText;
}

private function formatTransactionTextForTransaction(Transaction $transaction): string
{
    // Utilisez les méthodes getters de l'entité Transaction pour obtenir les valeurs nécessaires
    $date = $transaction->getDate()->format('Y-m-d H:i:s');
    $type = $transaction->getType();
    $quantity = $transaction->getQuantity();

    // Formate le texte de la transaction
    return "Date: $date, Type: $type, Quantity: $quantity\n";
}
    private function saveTransactionText(string $text)
    {
        $logFilePath = __DIR__ . '/../TransactionFile/log.txt';
        file_put_contents($logFilePath, $text . PHP_EOL, FILE_APPEND);
    }
}
