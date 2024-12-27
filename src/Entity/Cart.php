<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un panier d'achat
 * Gère la relation entre un utilisateur et ses articles
 */
#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $purchaseDate = null;

    #[ORM\Column]
    private bool $isPaid = false;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartItem::class, orphanRemoval: true)]
    private Collection $cartItems;

    /**
     * Constructeur initialisant un nouveau panier
     * Définit la date d'achat à maintenant et le statut non payé par défaut
     */
    public function __construct()
    {
        $this->cartItems = new ArrayCollection();
        $this->purchaseDate = new \DateTime();
        $this->isPaid = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(\DateTimeInterface $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;
        return $this;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;
        return $this;
    }

    /**
     * @return Collection<int, CartItem>
     */
    public function getCartItems(): Collection
    {
        return $this->cartItems;
    }

    /**
     * Ajoute un article au panier
     */
    public function addCartItem(CartItem $cartItem): self
    {
        if (!$this->cartItems->contains($cartItem)) {
            $this->cartItems->add($cartItem);
            $cartItem->setCart($this);
        }

        return $this;
    }

    /**
     * Retire un article du panier
     */
    public function removeCartItem(CartItem $cartItem): self
    {
        if ($this->cartItems->removeElement($cartItem)) {
            // Set the owning side to null (unless already changed)
            if ($cartItem->getCart() === $this) {
                $cartItem->setCart(null);
            }
        }

        return $this;
    }

    /**
     * Calcule le total du panier
     */
    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->cartItems as $item) {
            $total += $item->getProduct()->getPrice() * $item->getQuantity();
        }
        return $total;
    }

    /**
     * Compte le nombre total d'articles dans le panier
     */
    public function getTotalItems(): int
    {
        $total = 0;
        foreach ($this->cartItems as $item) {
            $total += $item->getQuantity();
        }
        return $total;
    }

    /**
     * Vérifie si le panier est vide
     */
    public function isEmpty(): bool
    {
        return $this->cartItems->isEmpty();
    }

    /**
     * Vide complètement le panier
     */
    public function clear(): self
    {
        foreach ($this->cartItems as $item) {
            $this->removeCartItem($item);
        }
        return $this;
    }

    /**
     * Vérifie si le stock est disponible pour tous les articles du panier
     */
    public function isStockAvailable(): bool
    {
        foreach ($this->cartItems as $item) {
            if ($item->getQuantity() > $item->getProduct()->getStock()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Valide le panier (marque comme payé et met à jour la date d'achat)
     */
    public function validate(): self
    {
        $this->isPaid = true;
        $this->purchaseDate = new \DateTime();
        return $this;
    }

    /**
     * Retourne un tableau des produits dont le stock est insuffisant
     * @return array<string, int> Tableau associatif [nom du produit => quantité manquante]
     */
    public function getOutOfStockItems(): array
    {
        $outOfStock = [];
        foreach ($this->cartItems as $item) {
            $missing = $item->getQuantity() - $item->getProduct()->getStock();
            if ($missing > 0) {
                $outOfStock[$item->getProduct()->getName()] = $missing;
            }
        }
        return $outOfStock;
    }

    /**
     * Retourne le statut du panier sous forme de chaîne
     */
    public function getStatus(): string
    {
        if ($this->isPaid) {
            return 'Payé';
        }
        return 'En cours';
    }

    /**
     * Crée une représentation string du panier
     */
    public function __toString(): string
    {
        return sprintf(
            'Panier #%d (%s) - %d article(s) - Total: %.2f €',
            $this->id,
            $this->getStatus(),
            $this->getTotalItems(),
            $this->getTotal()
        );
    }
}