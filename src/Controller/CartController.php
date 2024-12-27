<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/cart')]
#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = $entityManager->getRepository(Cart::class)->findOneBy([
            'user' => $user,
            'isPaid' => false
        ]);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    public function checkout(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cart = $entityManager->getRepository(Cart::class)->findOneBy([
            'user' => $user,
            'isPaid' => false
        ]);

        if (!$cart) {
            $this->addFlash('error', 'Aucun panier actif trouvé.');
            return $this->redirectToRoute('app_cart_index');
        }

        // Vérifier le stock pour chaque article
        foreach ($cart->getCartItems() as $cartItem) {
            $product = $cartItem->getProduct();
            if ($cartItem->getQuantity() > $product->getStock()) {
                $this->addFlash('error', 'Le produit '. $product->getName() .' n\'est plus disponible en quantité suffisante.');
                return $this->redirectToRoute('app_cart_index');
            }
        }

        // Mettre à jour les stocks
        foreach ($cart->getCartItems() as $cartItem) {
            $product = $cartItem->getProduct();
            $product->setStock($product->getStock() - $cartItem->getQuantity());
        }

        // Marquer le panier comme payé
        $cart->setIsPaid(true);
        $cart->setPurchaseDate(new \DateTime());

        // Sauvegarder les changements
        $entityManager->flush();

        $this->addFlash('success', 'Votre commande a été validée avec succès !');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function addToCart(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // Récupère ou crée un panier
        $cart = $entityManager->getRepository(Cart::class)->findOneBy([
            'user' => $user,
            'isPaid' => false
        ]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setIsPaid(false);
            $entityManager->persist($cart);
        }

        $quantity = $request->request->getInt('quantity', 1);

        // Vérifie la disponibilité du stock
        if ($quantity > $product->getStock()) {
            $this->addFlash('error', 'Stock insuffisant pour ce produit.');
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        // Vérifie si le produit est déjà dans le panier
        $cartItem = $entityManager->getRepository(CartItem::class)->findOneBy([
            'cart' => $cart,
            'product' => $product
        ]);

        if ($cartItem) {
            $newQuantity = $cartItem->getQuantity() + $quantity;
            if ($newQuantity > $product->getStock()) {
                $this->addFlash('error', 'La quantité demandée dépasse le stock disponible.');
                return $this->redirectToRoute('app_cart_index');
            }
            $cartItem->setQuantity($newQuantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($quantity);
            $entityManager->persist($cartItem);
        }

        $entityManager->flush();
        $this->addFlash('success', 'Produit ajouté au panier avec succès.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function removeFromCart(CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        // Vérification de sécurité
        if ($cartItem->getCart()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $entityManager->remove($cartItem);
        $entityManager->flush();

        $this->addFlash('success', 'Produit retiré du panier.');
        return $this->redirectToRoute('app_cart_index');
    }
}