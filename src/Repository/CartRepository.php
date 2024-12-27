<?php
// src/Repository/CartRepository.php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les opérations de base de données liées aux paniers.
 * 
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    /**
     * Constructeur du repository qui établit la connexion avec l'entité Cart.
     */
    public function __construct(ManagerRegistry $registry)
    {
        // On appelle le constructeur parent en spécifiant l'entité Cart
        parent::__construct($registry, Cart::class);
    }

    /**
     * Trouve tous les paniers qui ont été payés.
     * Cette méthode utilise le QueryBuilder pour créer une requête optimisée.
     *
     * @return Cart[] Retourne un tableau de paniers payés
     */
    public function findPaidCarts(): array
    {
        // Construction de la requête avec le QueryBuilder
        return $this->createQueryBuilder('c')
            // Ajoute la condition pour les paniers payés
            ->andWhere('c.isPaid = :paid')
            ->setParameter('paid', true)
            // Optimisation : charge les relations en une seule requête
            ->leftJoin('c.cartItems', 'ci')
            ->addSelect('ci')
            ->leftJoin('ci.product', 'p')
            ->addSelect('p')
            // Trie par date d'achat décroissante (plus récent d'abord)
            ->orderBy('c.purchaseDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve tous les paniers payés d'un utilisateur spécifique.
     *
     * @param int $userId L'ID de l'utilisateur
     * @return Cart[] Retourne un tableau des paniers de l'utilisateur
     */
    public function findUserPaidCarts(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :userId')
            ->andWhere('c.isPaid = :paid')
            ->setParameter('userId', $userId)
            ->setParameter('paid', true)
            ->leftJoin('c.cartItems', 'ci')
            ->addSelect('ci')
            ->leftJoin('ci.product', 'p')
            ->addSelect('p')
            ->orderBy('c.purchaseDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}