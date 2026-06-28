# Guide de Style Admin - Pizza Rico

Ce document définit les règles de design pour l'interface d'administration afin de garantir une cohérence visuelle parfaite.

## Système de Design (Variables CSS)

Toutes les propriétés doivent référencer les variables définies dans `assets/styles/adminApp.css`.

### Couleurs
- **Primaire** : `--admin-primary` (#f86c00be) - Utilisée pour les titres, icônes actives et boutons principaux.
- **Surface** : `--admin-surface` (#1a1d21) - Fond des cartes et de la sidebar.
- **Background** : `--admin-bg` (#0f1113) - Fond général de l'application.
- **Texte** : `--admin-text` (#e0e0e0) - Couleur de texte principale pour une lisibilité optimale sur fond sombre.

### Typographie
- **Police principale** : `Montserrat` (Sans-serif) - Utilisée pour tout le contenu textuel.
- **Titres** : `Playfair Display` (Serif) - Utilisée pour le branding dans la sidebar.

### Composants Unifiés

#### Boutons (`.btn-admin`)
- Utiliser systématiquement les classes `.btn-admin-primary`, `.btn-admin-success`, `.btn-admin-danger`.
- Les boutons sont arrondis (`border-radius-full`) et ont un effet de translation au survol.

#### Cartes (`.card`)
- Toutes les sections doivent être encapsulées dans des cartes avec un fond `--admin-surface`.
- Les en-têtes de cartes (`.card-header`) ont une légère transparence.

#### Tableaux (`.admin-table`)
- Utiliser la classe `.admin-table` pour unifier l'apparence des listes.
- Les en-têtes (`th`) sont en majuscules avec un espacement de lettres accentué.

#### Formulaires (`.form-control`)
- Les champs de saisie ont un fond sombre (`--admin-bg`) et une bordure discrète.
- Le focus applique une bordure de la couleur primaire avec un halo lumineux.

## Structure de la Page (Layout)

L'administration utilise un layout fixe défini dans `layout.html.twig` :
1. **Sidebar** (`.admin-sidebar`) : Fixe à gauche (260px).
2. **Contenu** (`.admin-content`) : Zone défilable à droite.
3. **Footer** (`.admin-footer`) : Toujours en bas du contenu.

## Responsive

- En dessous de **992px**, la sidebar est masquée par défaut et peut être affichée via un overlay.
- Les tableaux doivent être enveloppés dans `.table-responsive` pour garantir la lisibilité sur mobile.
