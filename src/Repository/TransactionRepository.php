<?php

namespace App\Repository;

use App\Entity\Crypto;
use App\Entity\User;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * Récupère toutes les transactions liées à une crypto donnée pour un utilisateur donné.
     *
     * @param User $user L'utilisateur concerné
     * @param Crypto $crypto La crypto concernée
     *
     * @return Transaction[] Un tableau d'objets Transaction
     */
    public function findAllTransactionsForCrypto(User $user, Crypto $crypto): array
    {
        return $this->createQueryBuilder('t')
        ->join('t.wallet', 'w') 
        ->andWhere('w.user = :user') 
        ->andWhere('t.crypto = :cryptoId')
        ->setParameter('user', $user)
        ->setParameter('cryptoId', $crypto->getId())
        ->getQuery()
        ->getResult();
    }
}
