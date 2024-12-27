<?php
// src/Controller/AdminOrderController.php

namespace App\Controller;

use App\Repository\CartRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}/admin')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class AdminOrderController extends AbstractController
{
    #[Route('/orders', name: 'app_admin_orders')]
    public function index(CartRepository $cartRepository, UserRepository $userRepository): Response
    {
        // Récupère tous les paniers payés
        $paidCarts = $cartRepository->findPaidCarts();
        
        // Récupère tous les utilisateurs avec leurs commandes
        $users = $userRepository->findAllWithCarts();

        return $this->render('admin/orders.html.twig', [
            'paid_carts' => $paidCarts,
            'users' => $users,
        ]);
    }

    #[Route('/orders/user/{id}', name: 'app_admin_user_orders')]
    public function userOrders(string $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->render('admin/user_orders.html.twig', [
            'user' => $user,
        ]);
    }
}