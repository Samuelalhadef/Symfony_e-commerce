<div align="center">
  ![png (7)](https://github.com/user-attachments/assets/2c8e8e2c-8fbb-48db-9a0b-a9dbb51521e7)
# 🛒 E-commerce Symfony Shop
Une application e-commerce moderne et sécurisée développée avec Symfony 6, offrant une expérience d'achat multilingue et intuitive.
</div>

## ✨ Fonctionnalités
- 🌍 Support multilingue (Français/Russe)
- 🔐 Système d'authentification sécurisé
- 👤 Gestion des rôles (ADMIN/USER)
- 🛍️ Gestion de panier avancée
- 💳 Système de paiement intégré
- 📱 Interface responsive
- 🎨 Design moderne avec Tailwind CSS

## 🛠️ Technologies Utilisées
- Symfony 6.4
- PHP 8.1
- MySQL
- Doctrine ORM
- Twig Template Engine
- Tailwind CSS
- VichUploader Bundle
- Webpack Encore

## 📦 Installation
1. **Clonez le dépôt**
   ```bash
   git clone https://github.com/votre-username/my-shop
   ```
2. **Installez les dépendances**
   ```bash
   composer install
   ```
3. **Configurez votre environnement**
   ```bash
   # Copiez le fichier .env et configurez vos variables
   cp .env .env.local
   ```
4. **Créez la base de données**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

## 📚 Structure du Projet
```
my-shop/
│
├── src/
│   ├── Controller/     # Contrôleurs de l'application
│   ├── Entity/         # Entités Doctrine
│   ├── Form/           # Types de formulaires
│   ├── Repository/     # Repositories Doctrine
│   └── Service/        # Services métier
│
├── templates/          # Templates Twig
├── translations/       # Fichiers de traduction
├── public/            # Assets publics
└── config/            # Configuration
```

## 🔋 Fonctionnalités Principales
### 👤 Gestion Utilisateurs
- Inscription/Connexion
- Profils utilisateurs
- Rôles et permissions
- Historique des commandes

### 🛍️ Gestion Produits
- Catalogue produits
- Recherche et filtrage
- Gestion des stocks
- Upload d'images

### 🛒 Panier et Commandes
- Panier persistant
- Calcul automatique des totaux
- Validation des stocks
- Historique des commandes

### 🌍 Internationalisation
- Interface multilingue
- Contenu traduit
- Détection automatique de la langue
- Sélecteur de langue

## 🔐 Sécurité
1. Protection CSRF
2. Hashage sécurisé des mots de passe
3. Validation des données
4. Gestion des permissions

## 🎨 Interface
- Design responsive
- Thème moderne avec Tailwind
- Messages flash stylisés
- Formulaires intuitifs

## 📫 Contact
- Portfolio: [votre-portfolio.com]
- LinkedIn: [votre-linkedin]
- Email: [votre-email]

---
<div align="center">
  
Fait avec ❤️ par [Samuel Alhadef](https://github.com/votre-username/)
</div>
