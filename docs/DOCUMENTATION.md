# Documentation BitChest — Guide Complet

> Ce document s'adresse à tout le monde, même sans connaissance technique.
> Il explique ce qu'est BitChest, comment il fonctionne, et comment l'utiliser.

---

## Table des matières

1. [Qu'est-ce que BitChest ?](#1-quest-ce-que-bitchest-)
2. [À qui s'adresse l'application ?](#2-à-qui-sadresse-lapplication-)
3. [Comment accéder à l'application ?](#3-comment-accéder-à-lapplication-)
4. [Comptes de connexion disponibles](#4-comptes-de-connexion-disponibles)
5. [Guide du client](#5-guide-du-client)
6. [Guide de l'administrateur](#6-guide-de-ladministrateur)
7. [Comprendre les crypto-monnaies dans BitChest](#7-comprendre-les-crypto-monnaies-dans-bitchest)
8. [Installation du projet (pour les développeurs)](#8-installation-du-projet-pour-les-développeurs)
9. [Structure du projet (vue d'ensemble)](#9-structure-du-projet-vue-densemble)
10. [Technologies utilisées](#10-technologies-utilisées)
11. [Sécurité](#11-sécurité)
12. [Questions fréquentes](#12-questions-fréquentes)

---

## 1. Qu'est-ce que BitChest ?

**BitChest** est une plateforme web de trading de crypto-monnaies. Elle permet à des particuliers d'acheter et de vendre des crypto-monnaies (comme le Bitcoin, l'Ethereum, etc.) depuis leur navigateur internet, sans avoir besoin d'installer quoi que ce soit.

### En résumé

- C'est comme une **banque en ligne**, mais pour les crypto-monnaies.
- Chaque client dispose d'un **portefeuille électronique** (wallet) avec un solde en euros.
- Il peut utiliser ce solde pour **acheter des cryptos**, et les **revendre** quand il le souhaite.
- Un **administrateur** gère la plateforme : il crée les comptes clients, gère les crypto-monnaies disponibles, etc.

### Ce que c'est vraiment

BitChest est un **prototype** (une version de démonstration) développé avec de vraies technologies professionnelles. Les prix des cryptos sont simulés (générés automatiquement), mais toute la logique d'achat, de vente, de calcul de portefeuille est réelle et fonctionnelle.

---

## 2. À qui s'adresse l'application ?

BitChest a **deux types d'utilisateurs** :

### L'Administrateur
C'est le gestionnaire de la plateforme. Il peut :
- Voir un tableau de bord avec les statistiques globales
- Créer, modifier et supprimer des comptes clients
- Activer ou désactiver un compte client
- Gérer la liste des crypto-monnaies disponibles
- Consulter le portefeuille de n'importe quel client

### Le Client
C'est le particulier qui investit. Il peut :
- S'inscrire lui-même ou être créé par l'administrateur
- Consulter son tableau de bord avec son solde et la valeur de ses investissements
- Acheter et vendre des crypto-monnaies
- Voir l'historique de toutes ses transactions
- Consulter les graphiques d'évolution des prix
- Modifier son profil

---

## 3. Comment accéder à l'application ?

### Depuis un navigateur (si le serveur est lancé)

Ouvrez votre navigateur (Chrome, Firefox, Edge...) et allez sur :

```
http://127.0.0.1:8000
```

Vous arriverez sur la **page de connexion**. Entrez votre email et votre mot de passe.

### Navigation automatique

Une fois connecté :
- Un **administrateur** est redirigé vers `/admin` (tableau de bord admin)
- Un **client** est redirigé vers `/client` (tableau de bord client)

### Déconnexion

Cliquez sur le bouton **"Logout"** / **"Se déconnecter"** dans le menu en haut à droite.

---

## 4. Comptes de connexion disponibles

Ces comptes sont créés automatiquement quand on charge les données de démonstration (`doctrine:fixtures:load`).

| Rôle           | Email                      | Mot de passe  | Solde initial |
|----------------|----------------------------|---------------|---------------|
| Administrateur | admin@bitchest.com         | `admin123`    | —             |
| Client 1       | jean.dupont@email.com      | `password123` | 500 €         |
| Client 2       | marie.martin@email.com     | `password123` | 1 500 €       |
| Client 3       | pierre.durand@email.com    | `password123` | 2 500 €       |
| Client 4       | sophie.bernard@email.com   | `password123` | 750 €         |
| Client 5       | thomas.petit@email.com     | `password123` | 3 000 €       |

> **Note :** Ces mots de passe sont uniquement pour la démonstration. En production, ils doivent être changés immédiatement.

---

## 5. Guide du client

### 5.1 Se connecter

1. Aller sur `http://127.0.0.1:8000`
2. Entrer son email et son mot de passe
3. Cliquer sur **"Sign In"**

### 5.2 Tableau de bord (Dashboard)

Après connexion, le client voit son tableau de bord qui affiche :

| Indicateur       | Explication                                              |
|------------------|----------------------------------------------------------|
| **Solde**        | L'argent disponible en euros pour acheter des cryptos    |
| **Valeur totale**| La valeur actuelle de toutes ses cryptos                 |
| **Profit/Perte** | Ce qu'il a gagné ou perdu par rapport à ses achats       |
| **Valeur nette** | Solde + valeur des cryptos = patrimoine total            |

Il voit aussi la liste de ses cryptos actuellement détenues (son **portefeuille**).

### 5.3 Voir les crypto-monnaies disponibles

Dans le menu de gauche, cliquez sur **"Cryptocurrencies"** (ou `/client/cryptocurrencies`).

Vous verrez la liste des 10 cryptos disponibles avec leur prix actuel.

### 5.4 Voir le détail d'une crypto

Cliquez sur une crypto dans la liste. Vous verrez :
- Le **prix actuel**
- La **variation sur 24h** (hausse ou baisse en % et en €)
- Le **graphique d'évolution des prix** sur 30 jours
- Un bouton **"Buy"** pour acheter

### 5.5 Acheter une crypto-monnaie

1. Depuis la page de détail d'une crypto, cliquez sur **"Buy"**
2. Entrez la **quantité** que vous souhaitez acheter (ex: 0.5 BTC)
3. Le coût total est calculé automatiquement
4. Cliquez sur **"Confirm Purchase"**
5. Votre solde est débité et la crypto est ajoutée à votre portefeuille

> **Règle :** Vous ne pouvez pas acheter plus que ce que votre solde permet.

### 5.6 Vendre une crypto-monnaie

1. Depuis votre portefeuille, cliquez sur une crypto que vous possédez
2. Cliquez sur **"Sell"**
3. Entrez la quantité à vendre
4. Confirmez — votre solde est crédité en euros

> **Règle :** Vous ne pouvez pas vendre plus que ce que vous possédez.

### 5.7 Voir son portefeuille

Dans le menu, cliquez sur **"Wallet"** (`/client/wallet`). Vous verrez :
- Votre **solde en euros**
- La liste de vos **cryptos détenues** (quantité, prix moyen d'achat, valeur actuelle, profit/perte)
- Les dernières **transactions d'achat**

### 5.8 Voir l'historique des transactions

Dans le portefeuille, cliquez sur **"Transaction History"** (`/client/wallet/transactions`).
Vous verrez **toutes** vos opérations (achats et ventes) avec la date, la quantité et le montant.

### 5.9 Modifier son profil

Dans le menu, cliquez sur **"Profile"** (`/client/profile`). Vous pouvez modifier :
- Votre prénom et nom
- Votre email
- Votre mot de passe

---

## 6. Guide de l'administrateur

### 6.1 Tableau de bord admin

Après connexion avec un compte admin, vous arrivez sur `/admin`. Vous voyez :
- Le **nombre total de clients**
- Le **nombre de crypto-monnaies** disponibles
- Les **dernières transactions** effectuées sur la plateforme

### 6.2 Gérer les clients

Accès : menu **"Clients"** → `/admin/clients`

#### Créer un nouveau client

1. Cliquez sur **"New Client"**
2. Remplissez : prénom, nom, email, téléphone (optionnel), adresse (optionnel)
3. Cliquez sur **"Create"**
4. Un **mot de passe temporaire** est généré automatiquement et affiché à l'écran
5. Un **portefeuille de 500 €** est créé automatiquement pour le nouveau client
6. Communiquez ce mot de passe temporaire au client, qui pourra le changer depuis son profil

> **Important :** Notez le mot de passe temporaire — il n'est affiché qu'une seule fois.

#### Voir les détails d'un client

Cliquez sur l'icône **œil** (👁) à côté du client. Vous verrez son profil complet avec :
- Son solde en euros
- La valeur totale de son portefeuille
- Son profit/perte total
- Sa valeur nette (patrimoine total)

#### Modifier un client

Cliquez sur l'icône **crayon** (✏️) pour modifier les informations du client (nom, email, téléphone...).

#### Activer / Désactiver un client

Cliquez sur l'icône **pause/play** (⏸/▶) pour changer le statut du client :
- **Actif** = le client peut se connecter normalement
- **Inactif** = le compte est suspendu

#### Supprimer un client

Cliquez sur l'icône **poubelle** (🗑️). Une fenêtre de confirmation s'ouvre. Confirmez pour supprimer définitivement le compte.

> **Attention :** La suppression est irréversible. Toutes les données du client (portefeuille, transactions) sont également supprimées.

### 6.3 Gérer les crypto-monnaies

Accès : menu **"Cryptocurrencies"** → `/admin/cryptocurrencies`

#### Liste des cryptos

Vous voyez toutes les cryptos disponibles avec leur symbole, nom et prix actuel.

#### Ajouter une crypto-monnaie

1. Cliquez sur **"New Cryptocurrency"**
2. Remplissez : symbole (ex: BTC), nom (ex: Bitcoin), prix actuel, image (optionnel)
3. Cliquez sur **"Create Cryptocurrency"**

#### Modifier une crypto

Cliquez sur l'icône **crayon** pour modifier le nom, le prix ou l'image.

#### Supprimer une crypto

Cliquez sur l'icône **poubelle**. La suppression est bloquée si des clients possèdent encore cette crypto ou ont des transactions associées.

### 6.4 Gérer son propre profil

Accès : menu **"My Profile"** → `/admin/profile`

L'administrateur peut modifier son prénom, nom, email et mot de passe.

---

## 7. Comprendre les crypto-monnaies dans BitChest

### Les 10 cryptos disponibles

| Symbole | Nom        | Prix de base |
|---------|------------|--------------|
| BTC     | Bitcoin    | ~42 500 €    |
| ETH     | Ethereum   | ~2 280 €     |
| XRP     | Ripple     | ~0,52 €      |
| BCH     | Bitcoin Cash| ~235 €      |
| ADA     | Cardano    | ~0,48 €      |
| LTC     | Litecoin   | ~68,50 €     |
| DASH    | Dash       | ~27,50 €     |
| IOTA    | IOTA       | ~0,18 €      |
| XEM     | NEM        | ~0,025 €     |
| XLM     | Stellar    | ~0,11 €      |

### Comment les prix évoluent

Dans BitChest, les prix sont **simulés** : chaque jour, le système génère automatiquement un nouveau prix qui varie de **-5% à +5%** par rapport au prix du jour précédent. Cela imite le comportement réel des marchés crypto.

Pour générer les cotations manuellement :
```bash
php bin/console app:generate-quotes
```

### Calcul du profit/perte

Quand vous achetez plusieurs fois la même crypto à des prix différents, BitChest calcule un **prix moyen d'achat**. Le profit ou la perte est la différence entre la valeur actuelle et ce prix moyen.

**Exemple :**
- Achat 1 : 1 BTC à 40 000 € → coût : 40 000 €
- Achat 2 : 1 BTC à 44 000 € → coût : 44 000 €
- Prix moyen : 42 000 €
- Prix actuel : 45 000 €
- **Profit : (45 000 - 42 000) × 2 = +6 000 €**

---

## 8. Installation du projet (pour les développeurs)

### Prérequis

| Outil         | Version  | Installation                              |
|---------------|----------|-------------------------------------------|
| PHP           | 8.2+     | https://windows.php.net/download          |
| Composer      | récente  | https://getcomposer.org/download          |
| MySQL         | 8.0      | Via XAMPP : https://www.apachefriends.org |
| Symfony CLI   | récente  | https://symfony.com/download              |
| Git           | récente  | https://git-scm.com/downloads             |

### Étapes d'installation

```bash
# 1. Cloner le projet
git clone https://github.com/Raoul-Ekwa/bitChest.git
cd bitChest

# 2. Installer les dépendances PHP
composer install

# 3. Configurer la base de données
cp .env .env.local
# Éditer .env.local et mettre votre mot de passe MySQL :
# DATABASE_URL="mysql://root:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/bitchest?serverVersion=8.0.32&charset=utf8mb4"

# 4. Créer la base de données et les tables
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Charger les données de démonstration
php bin/console doctrine:fixtures:load

# 6. Installer les assets (CSS/JS)
php bin/console importmap:install

# 7. Lancer le serveur
symfony server:start
```

Ouvrir ensuite : **http://127.0.0.1:8000**

### Lancer les tests

```bash
# Créer la base de données de test (une seule fois)
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test

# Lancer tous les tests
php bin/phpunit

# Lancer un test spécifique
php bin/phpunit --filter NomDuTest
```

### Commandes utiles

```bash
php bin/console cache:clear              # Vider le cache
php bin/console app:generate-quotes      # Générer les cotations du jour
php bin/console doctrine:fixtures:load   # Recharger les données de démo
```

---

## 9. Structure du projet (vue d'ensemble)

```
bitChest/
├── src/
│   ├── Controller/
│   │   ├── Admin/              ← Contrôleurs de l'interface admin
│   │   │   ├── AdminDashboardController.php
│   │   │   ├── AdminProfileController.php
│   │   │   ├── ClientManagementController.php
│   │   │   └── CryptocurrencyManagementController.php
│   │   ├── Client/             ← Contrôleurs de l'interface client
│   │   │   ├── ClientDashboardController.php
│   │   │   ├── ClientProfileController.php
│   │   │   ├── WalletController.php
│   │   │   └── TransactionController.php
│   │   ├── SecurityController.php   ← Connexion / déconnexion
│   │   └── RegistrationController.php
│   ├── Entity/                 ← Modèles de données (tables SQL)
│   │   ├── User.php, Administrator.php, Client.php
│   │   ├── Wallet.php, Holding.php, Transaction.php
│   │   ├── Cryptocurrency.php, Quote.php
│   ├── Form/                   ← Formulaires
│   ├── Service/                ← Logique métier
│   │   ├── CalculationService.php    ← Calculs portefeuille
│   │   ├── TransactionService.php    ← Achat/vente
│   │   ├── WalletService.php         ← Gestion solde
│   │   ├── CryptocurrencyService.php ← Données crypto
│   │   ├── QuoteGeneratorService.php ← Génération des prix
│   │   └── PasswordGeneratorService.php
│   ├── Repository/             ← Requêtes en base de données
│   └── DataFixtures/           ← Données de démonstration
├── templates/                  ← Pages HTML (Twig)
│   ├── admin/                  ← Pages de l'interface admin
│   ├── client/                 ← Pages de l'interface client
│   └── security/               ← Pages connexion/inscription
├── migrations/                 ← Scripts de création de tables SQL
├── tests/                      ← Tests automatisés
│   ├── Unit/                   ← Tests unitaires
│   └── Functional/             ← Tests fonctionnels (simulation navigateur)
├── public/                     ← Dossier web (index.php, assets)
├── .env                        ← Configuration de base
└── .env.local                  ← ⚠️ Vos identifiants locaux (non commité)
```

---

## 10. Technologies utilisées

| Technologie        | Rôle                                        |
|--------------------|---------------------------------------------|
| **PHP 8.2**        | Langage de programmation côté serveur       |
| **Symfony 7.4**    | Framework PHP — structure et organisation   |
| **Doctrine ORM**   | Communication avec la base de données       |
| **MySQL 8.0**      | Base de données relationnelle               |
| **Twig**           | Moteur de templates (génère le HTML)        |
| **Bootstrap 5**    | Design et mise en page responsive           |
| **Chart.js**       | Graphiques interactifs (courbes de prix)    |
| **Symfony Security**| Authentification et gestion des rôles      |
| **Stimulus.js**    | Interactivité JavaScript légère             |
| **PHPUnit**        | Tests automatisés                           |

---

## 11. Sécurité

BitChest applique les bonnes pratiques de sécurité web standard :

| Menace              | Protection appliquée                                          |
|---------------------|---------------------------------------------------------------|
| **Vol de session**  | Cookies sécurisés, sessions Symfony                          |
| **Mots de passe**   | Jamais stockés en clair — hashage bcrypt                     |
| **XSS**             | Twig échappe automatiquement toutes les variables            |
| **CSRF**            | Jeton de sécurité sur tous les formulaires sensibles         |
| **Injection SQL**   | Doctrine ORM — jamais de SQL écrit à la main                |
| **Accès non autorisé** | Chaque route vérifie le rôle (`ROLE_ADMIN` / `ROLE_CLIENT`) |

---

## 12. Questions fréquentes

**Q : Je ne vois pas de style sur le site (page sans CSS)**
→ Relancer : `php bin/console importmap:install`

**Q : Erreur "Access denied" à la base de données**
→ Vérifier le mot de passe dans `.env.local` (pas dans `.env`)

**Q : Le serveur ne démarre pas**
→ Vérifier que PHP est installé : `php -v` et que MySQL est démarré (XAMPP)

**Q : Comment créer un nouveau client ?**
→ Connectez-vous en admin → menu "Clients" → bouton "New Client"

**Q : Un client peut-il s'inscrire tout seul ?**
→ Oui, depuis la page d'accueil, lien "Register here" sous le formulaire de connexion

**Q : Comment voir les graphiques de prix ?**
→ Connectez-vous en client → "Cryptocurrencies" → cliquez sur une crypto

**Q : Les prix sont-ils réels ?**
→ Non. Dans ce prototype, les prix sont simulés avec une variation aléatoire de ±5% par jour.

**Q : Puis-je perdre de l'argent réel ?**
→ Non. BitChest est un prototype de démonstration. Aucun vrai argent n'est impliqué.

---

*Documentation générée pour le projet BitChest — Symfony 7.4 / PHP 8.2*
