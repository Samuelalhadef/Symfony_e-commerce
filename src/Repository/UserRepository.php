<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les opérations de base de données liées aux utilisateurs.
 * Ce repository étend ServiceEntityRepository pour profiter des fonctionnalités 
 * de base de Doctrine tout en étant spécifique à l'entité User.
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * Le constructeur initialise le repository avec l'entité User.
     * Cette injection est nécessaire pour que Doctrine sache quelle table utiliser.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Trouve tous les utilisateurs avec leurs paniers.
     * Cette méthode utilise les jointures pour optimiser les requêtes.
     * 
     * @return User[] Retourne un tableau d'utilisateurs avec leurs paniers
     */
    public function findAllWithCarts(): array
    {
        return $this->createQueryBuilder('u')
            // Jointure avec les paniers
            ->leftJoin('u.carts', 'c')
            ->addSelect('c')
            // Jointure avec les articles du panier
            ->leftJoin('c.cartItems', 'ci')
            ->addSelect('ci')
            // Jointure avec les produits
            ->leftJoin('ci.product', 'p')
            ->addSelect('p')
            // Tri par nom de famille
            ->orderBy('u.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les utilisateurs qui ont des commandes payées.
     * Utile pour le suivi des clients actifs.
     */
    public function findUsersWithPaidOrders(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.carts', 'c')
            ->andWhere('c.isPaid = :isPaid')
            ->setParameter('isPaid', true)
            ->groupBy('u.id')
            ->orderBy('u.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des utilisateurs par critères multiples.
     * Permet une recherche flexible basée sur différents paramètres.
     */
    public function findBySearchCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('u');

        if (!empty($criteria['email'])) {
            $qb->andWhere('u.email LIKE :email')
               ->setParameter('email', '%' . $criteria['email'] . '%');
        }

        if (!empty($criteria['name'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('u.firstName', ':name'),
                    $qb->expr()->like('u.lastName', ':name')
                )
            )->setParameter('name', '%' . $criteria['name'] . '%');
        }

        if (isset($criteria['hasOrders'])) {
            $qb->leftJoin('u.carts', 'c')
               ->andWhere('c.isPaid = :isPaid')
               ->setParameter('isPaid', true);
        }

        return $qb->orderBy('u.lastName', 'ASC')
                 ->getQuery()
                 ->getResult();
    }
}