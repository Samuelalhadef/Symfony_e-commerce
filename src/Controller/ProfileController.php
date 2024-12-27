<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Le constructeur permet d'injecter l'EntityManager qui sera utilisé dans toute la classe
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        // On récupère l'utilisateur connecté via le système de sécurité de Symfony
        /** @var User $user */
        $user = $this->getUser();

        // On récupère tous les paniers payés de l'utilisateur
        $purchasedCarts = $this->entityManager->getRepository(Cart::class)->findBy(
            [
                'user' => $user,
                'isPaid' => true
            ],
            ['purchaseDate' => 'DESC'] // Trie par date d'achat décroissante
        );

        // On passe les données à la vue
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'purchasedCarts' => $purchasedCarts,
        ]);
    }

    #[Route('/order/{id}', name: 'app_profile_order_details')]
    public function orderDetails(Cart $cart): Response
    {
        // Vérifie que l'utilisateur actuel est bien le propriétaire de la commande
        if ($cart->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette commande.');
        }

        // Vérifie que la commande a bien été payée
        if (!$cart->isPaid()) {
            throw $this->createNotFoundException('Cette commande n\'existe pas.');
        }

        return $this->render('profile/order_details.html.twig', [
            'cart' => $cart
        ]);
    }
}