# Cahier des Charges — BitChest
## Exigences du projet et état de réalisation

> Ce document liste toutes les fonctionnalités demandées pour le projet BitChest
> et indique pour chacune si elle a été **réalisée**, **partiellement réalisée** ou **non réalisée**.

---

## Légende

| Symbole | Signification              |
|---------|----------------------------|
| ✅      | Réalisé et fonctionnel     |
| ⚠️      | Partiellement réalisé      |
| ❌      | Non réalisé                |

---

## 1. Fonctionnalités Administrateur

| ID  | Fonctionnalité           | Description demandée                                            | État | Notes                                                                 |
|-----|--------------------------|-----------------------------------------------------------------|------|-----------------------------------------------------------------------|
| A01 | Authentification         | Connexion sécurisée avec email et mot de passe                  | ✅   | Formulaire login, hashage bcrypt, session Symfony                     |
| A02 | Tableau de bord          | Vue d'ensemble : nb clients, cryptos, dernières transactions    | ✅   | `/admin` — statistiques globales et transactions récentes             |
| A03 | Gestion du profil admin  | Modifier ses données personnelles et son mot de passe           | ✅   | `/admin/profile` et `/admin/profile/edit`                             |
| A04 | Liste des clients        | Afficher tous les clients avec statut et solde                  | ✅   | `/admin/clients` — tableau avec nom, email, téléphone, solde, statut  |
| A05 | Créer un client          | Ajouter un client avec mot de passe temporaire généré           | ✅   | Wallet de 500 € créé automatiquement, mot de passe affiché une fois   |
| A06 | Modifier un client       | Éditer les informations personnelles d'un client                | ✅   | `/admin/clients/{id}/edit`                                            |
| A07 | Supprimer un client      | Supprimer un compte client avec confirmation                    | ✅   | Modal de confirmation CSRF, suppression en cascade du wallet          |
| A08 | Activer / Désactiver     | Changer le statut actif/inactif d'un client                     | ✅   | Bouton toggle avec protection CSRF                                    |
| A09 | Voir les détails client  | Consulter le portefeuille et les stats d'un client              | ✅   | `/admin/clients/{id}` — solde, valeur portfolio, P&L, valeur nette    |
| A10 | Liste des crypto-monnaies| Afficher toutes les cryptos et leur cours actuel                | ✅   | `/admin/cryptocurrencies`                                             |
| A11 | Créer une crypto         | Ajouter une nouvelle crypto-monnaie                             | ✅   | `/admin/cryptocurrencies/new` — symbole, nom, prix, image             |
| A12 | Modifier une crypto      | Éditer les informations d'une crypto                            | ✅   | `/admin/cryptocurrencies/{id}/edit`                                   |
| A13 | Supprimer une crypto     | Supprimer une crypto (si aucun holding/transaction actif)       | ✅   | Vérification des dépendances avant suppression, flash d'erreur sinon  |

---

## 2. Fonctionnalités Client

| ID  | Fonctionnalité          | Description demandée                                      | État | Notes                                                                   |
|-----|-------------------------|-----------------------------------------------------------|------|-------------------------------------------------------------------------|
| C01 | Inscription             | Créer un compte avec email et mot de passe                | ✅   | `/register` — formulaire avec validation, wallet créé à l'inscription   |
| C02 | Authentification        | Connexion sécurisée avec email et mot de passe            | ✅   | Même page de login que l'admin, redirection automatique selon le rôle   |
| C03 | Tableau de bord client  | Vue portefeuille : solde, valeur, P&L, valeur nette       | ✅   | `/client` — 4 indicateurs clés + liste des holdings actuels             |
| C04 | Gestion du profil       | Modifier ses données personnelles et son mot de passe     | ✅   | `/client/profile` et `/client/profile/edit`                             |
| C05 | Voir son portefeuille   | Afficher les cryptos détenues et les transactions         | ✅   | `/client/wallet` — holdings avec quantité, prix moyen, valeur, P&L      |
| C06 | Liste des cryptos       | Voir toutes les cryptos disponibles avec leur prix        | ✅   | `/client/cryptocurrencies` — liste avec prix actuel et variation 24h    |
| C07 | Détail d'une crypto     | Voir le prix, la variation et le graphique sur 30 jours   | ✅   | `/client/cryptocurrencies/{id}` — graphique Chart.js intégré            |
| C08 | Acheter une crypto      | Acheter une quantité au cours actuel, débiter le solde    | ✅   | `/client/buy/{id}` — validation solde suffisant, mise à jour holding    |
| C09 | Vendre une crypto       | Vendre ses cryptos et créditer le solde en euros          | ✅   | `/client/sell/{id}` — validation quantité possédée, mise à jour holding |
| C10 | Historique transactions | Voir l'historique complet des achats et ventes            | ✅   | `/client/wallet/transactions` — toutes les opérations avec date/montant |

---

## 3. Exigences Techniques

### 3.1 Architecture et framework

| Exigence                            | État | Notes                                                          |
|-------------------------------------|------|----------------------------------------------------------------|
| PHP 8.2 minimum                     | ✅   | Typages stricts, attributs PHP 8, fonctions modernes utilisées |
| Symfony 7.x                         | ✅   | Version 7.4 — dernière version stable                          |
| MySQL 8.0                           | ✅   | Configuré via Doctrine DBAL                                    |
| Doctrine ORM 3.x                    | ✅   | Entités, migrations, repositories                              |
| Architecture multicouche            | ✅   | Présentation → Contrôleur → Service → Repository → BDD         |
| Séparation logique métier/contrôleur| ✅   | 6 services dédiés, contrôleurs allégés                         |

### 3.2 Base de données

| Exigence                                  | État | Notes                                                          |
|-------------------------------------------|------|----------------------------------------------------------------|
| Table `user` (héritage joint)             | ✅   | Joined Table Inheritance — `administrator` + `client`          |
| Table `wallet` (1 par client)             | ✅   | OneToOne avec Client, solde DECIMAL(15,2)                      |
| Table `holding` (crypto détenue)          | ✅   | Contrainte unique (wallet_id, crypto_id)                       |
| Table `transaction` (historique)          | ✅   | Types buy/sell, prix à la transaction conservé                 |
| Table `cryptocurrency`                    | ✅   | Symbole unique, prix en DECIMAL(18,8)                          |
| Table `quote` (historique des prix)       | ✅   | Index sur (crypto_id, created_at) pour performance             |
| Valeurs monétaires en DECIMAL (bcmath)    | ✅   | Aucun calcul en float — bcadd, bcsub, bcmul, bcdiv             |
| Migrations Doctrine                       | ✅   | Historique complet des migrations                              |

### 3.3 Sécurité

| Exigence                                      | État | Notes                                                          |
|-----------------------------------------------|------|----------------------------------------------------------------|
| Authentification par email/mot de passe       | ✅   | Symfony Security avec LoginFormAuthenticator                   |
| Hashage des mots de passe (bcrypt)            | ✅   | UserPasswordHasherInterface                                    |
| Contrôle d'accès par rôle (RBAC)             | ✅   | `ROLE_ADMIN` et `ROLE_CLIENT` sur chaque route                 |
| Protection CSRF sur les formulaires           | ✅   | Tokens CSRF sur tous les formulaires de modification           |
| Protection XSS                                | ✅   | Twig auto-escape activé                                        |
| Protection injection SQL                      | ✅   | Doctrine ORM — requêtes préparées                              |
| Mots de passe temporaires                     | ✅   | PasswordGeneratorService — 12 chars aléatoires                 |
| Credentials dans variables d'environnement    | ✅   | `.env.local` ignoré par Git                                    |

### 3.4 Frontend

| Exigence                               | État | Notes                                                                |
|----------------------------------------|------|----------------------------------------------------------------------|
| Interface responsive (mobile/tablet)   | ✅   | Bootstrap 5 — grille responsive sur toutes les pages                 |
| Graphiques d'évolution des prix        | ✅   | Chart.js — courbe des 30 derniers jours sur la page détail crypto    |
| Sidebar de navigation                  | ✅   | Navigation latérale cohérente sur toutes les pages admin et client   |
| Modals de confirmation (suppression)   | ✅   | Bootstrap Modal pour les suppressions (clients et cryptos)           |
| Messages flash (succès/erreur)         | ✅   | Notifications visuelles après chaque action                          |
| Pas de Node.js / build step            | ✅   | Symfony Asset Mapper — pas de npm, pas de webpack                    |

### 3.5 Génération des cotations

| Exigence                                        | État | Notes                                                         |
|-------------------------------------------------|------|---------------------------------------------------------------|
| Prix de base par crypto (BTC, ETH, etc.)        | ✅   | QuoteGeneratorService — prix de référence définis             |
| Variation journalière de ±5%                    | ✅   | Algorithme de variation aléatoire dans QuoteGeneratorService  |
| Historique sur 30 jours (données initiales)     | ✅   | CryptocurrencyFixtures génère 30 jours d'historique           |
| Commande console pour générer les cotations     | ✅   | `php bin/console app:generate-quotes`                         |

---

## 4. Tests

| Type de test                                    | Fichier                                              | Nb tests | État |
|-------------------------------------------------|------------------------------------------------------|----------|------|
| Tests unitaires — Entité `User`                 | `tests/Unit/Entity/UserTest.php`                     | 10       | ✅   |
| Tests unitaires — Entité `Cryptocurrency`       | `tests/Unit/Entity/CryptocurrencyTest.php`           | 15       | ✅   |
| Tests unitaires — Service `CalculationService`  | `tests/Unit/Service/CalculationServiceTest.php`      | 16       | ✅   |
| Tests unitaires — Service `WalletService`       | `tests/Unit/Service/WalletServiceTest.php`           | 9        | ✅   |
| Tests fonctionnels — Contrôle d'accès général   | `tests/Functional/Controller/TransactionControllerTest.php` | 8  | ✅   |
| Tests fonctionnels — CRUD Cryptocurrency admin  | `tests/Functional/Controller/Admin/CryptocurrencyManagementControllerTest.php` | 15 | ✅ |

**Total : ~73 tests automatisés**

---

## 5. Données de démonstration (Fixtures)

| Donnée                              | Quantité | État |
|-------------------------------------|----------|------|
| Compte administrateur               | 1        | ✅   |
| Comptes clients avec wallet         | 5        | ✅   |
| Crypto-monnaies                     | 10       | ✅   |
| Historique de prix (quotes)         | 30 j × 10 cryptos = ~300 | ✅ |

---

## 6. Ce qui n'a pas été demandé mais a été ajouté

Ces fonctionnalités ont été ajoutées au-delà du cahier des charges initial pour améliorer la qualité du projet :

| Ajout                                              | Justification                                         |
|----------------------------------------------------|-------------------------------------------------------|
| CRUD complet des crypto-monnaies côté admin        | Le cahier initial demandait seulement la liste (A10)  |
| Variation 24h sur la page détail crypto            | Améliore l'expérience utilisateur client              |
| Contrainte d'unicité (wallet × crypto) sur holding | Évite la duplication des données                      |
| Index SQL sur transaction et quote                 | Optimisation des performances des requêtes            |
| PasswordGeneratorService                           | Séparation propre de la logique de génération         |
| 73+ tests automatisés (unitaires + fonctionnels)   | Assurance qualité et non-régression                   |
| Calcul du prix moyen d'achat pondéré               | Calcul financier précis avec bcmath                   |
| Protection de suppression (crypto avec holdings)   | Intégrité des données                                 |

---

## 7. Récapitulatif global

| Catégorie                    | Total demandé | Réalisé | Partiel | Non réalisé |
|------------------------------|---------------|---------|---------|-------------|
| Fonctionnalités Admin        | 10            | 13 *    | 0       | 0           |
| Fonctionnalités Client       | 10            | 10      | 0       | 0           |
| Exigences techniques         | 28            | 28      | 0       | 0           |
| Tests                        | —             | 73+     | —       | —           |

> *3 fonctionnalités admin supplémentaires ajoutées (create/edit/delete crypto)

### Conclusion

**Toutes les exigences du cahier des charges sont réalisées et fonctionnelles.**
Le projet dépasse les exigences initiales sur plusieurs points (CRUD crypto complet, tests exhaustifs, calculs financiers précis).

---

*Document établi pour le projet BitChest — version Symfony 7.4 / PHP 8.2*
