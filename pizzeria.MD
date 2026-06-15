# Liste des tâches pour terminer l'application de pizzeria

## Fonctionnalités prioritaires

### 1. Système de filtrage des pizzas par catégorie
- Ajouter des attributs de catégorie aux entités Pizza (végétarienne, viande, etc.)
- Créer une interface de filtrage sur la page des pizzas
- Implémenter la logique de filtrage dans le contrôleur

### 2. Page de détail pour chaque pizza
- Créer une nouvelle route et template pour afficher les détails d'une pizza
- Ajouter des informations supplémentaires comme la description complète, les allergènes
- Permettre la personnalisation (ajout/suppression d'ingrédients)

### 3. Système de panier d'achat
- Développer un service de gestion de panier avec session
- Créer une interface utilisateur pour le panier
- Implémenter les fonctionnalités d'ajout, suppression et modification des quantités

### 4. Processus de paiement en ligne
- Intégrer une passerelle de paiement sécurisée (Stripe, PayPal)
- Créer le flux de paiement et de confirmation
- Gérer les notifications de paiement et les reçus

## Fonctionnalités secondaires

### 5. Système de compte utilisateur
- Implémenter l'authentification et l'enregistrement
- Créer un espace personnel avec historique des commandes
- Permettre la sauvegarde d'adresses de livraison

### 6. Section boissons et accompagnements
- Créer de nouvelles entités pour ces produits
- Développer l'interface utilisateur pour les afficher
- Intégrer ces produits au système de panier

### 7. Page de contact avec formulaire
- Créer un formulaire de contact avec validation
- Intégrer une carte de localisation (Google Maps ou OpenStreetMap)
- Ajouter les informations de contact et horaires d'ouverture

### 8. Fonctionnalité de réservation de table
- Développer un système de gestion des disponibilités
- Créer un formulaire de réservation avec sélection de date et heure
- Implémenter les confirmations par email

## Améliorations finales

### 9. Système de notation et d'avis
- Permettre aux utilisateurs connectés de laisser des avis
- Implémenter un système de notation par étoiles
- Afficher la moyenne des notes sur les cartes de pizzas

### 10. Optimisation SEO
- Améliorer les balises meta et la structure des pages
- Optimiser les temps de chargement
- Créer un sitemap et un fichier robots.txt