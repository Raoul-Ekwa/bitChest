# BitChest — Plateforme de trading de crypto-monnaies

Application web de trading de crypto-monnaies construite avec **Symfony 7.4** et **PHP 8.2+**.

---

## Prérequis — Outils à installer

### 1. PHP 8.2 ou supérieur
Télécharger : https://windows.php.net/download (Windows) ou https://www.php.net/downloads (Mac/Linux)

**Windows :** Télécharger la version **Thread Safe x64** → extraire dans `C:\php`

Ajouter au PATH Windows :
```
Panneau de configuration → Système → Paramètres système avancés
→ Variables d'environnement → Path → Nouveau → C:\php
```

Vérifier l'installation :
```bash
php -v
# PHP 8.2.x ou supérieur
```

Extensions PHP requises (activer dans `php.ini` en décommentant les lignes) :
```ini
extension=ctype
extension=iconv
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=intl
extension=sodium
```

---

### 2. Composer (gestionnaire de dépendances PHP)
Télécharger : https://getcomposer.org/download

**Windows :** Utiliser l'installeur `Composer-Setup.exe` — il configure le PATH automatiquement.

Vérifier :
```bash
composer --version
```

---

### 3. MySQL 8.0
**Option recommandée — XAMPP** (inclut MySQL + phpMyAdmin) :
Télécharger : https://www.apachefriends.org/fr/download.html

Après installation, démarrer **MySQL** depuis le panneau XAMPP.

Ajouter au PATH (Windows) : `C:\xampp\mysql\bin`

Vérifier :
```bash
mysql --version
```

---

### 4. Symfony CLI (serveur de développement)
Télécharger : https://symfony.com/download

**Windows :** Télécharger `symfony.exe` → placer dans `C:\symfony` → ajouter au PATH.

Vérifier :
```bash
symfony version
```

---

### 5. Git
Télécharger : https://git-scm.com/downloads

Vérifier :
```bash
git --version
```

---

## Installation du projet

### Étape 1 — Cloner le dépôt
```bash
git clone https://github.com/Raoul-Ekwa/bitChest.git
cd bitChest
```

### Étape 2 — Installer les dépendances PHP
```bash
composer install
```
> Cette commande télécharge tous les packages listés dans `composer.json` dans le dossier `vendor/`.

### Étape 3 — Configurer l'environnement
Créer le fichier `.env.local` à la racine du projet (il ne sera pas commité) :
```bash
# Copier le fichier de base
cp .env .env.local
```

Éditer `.env.local` et modifier la ligne `DATABASE_URL` avec vos identifiants MySQL :
```dotenv
DATABASE_URL="mysql://root:VOTRE_MOT_DE_PASSE@127.0.0.1:3306/bitchest?serverVersion=8.0.32&charset=utf8mb4"
```

> Si vous utilisez XAMPP avec le mot de passe par défaut (vide), mettez :
> ```dotenv
> DATABASE_URL="mysql://root:@127.0.0.1:3306/bitchest?serverVersion=8.0.32&charset=utf8mb4"
> ```

### Étape 4 — Créer la base de données
```bash
php bin/console doctrine:database:create
```

### Étape 5 — Créer les tables (migrations)
```bash
php bin/console doctrine:migrations:migrate
```
Répondre `yes` à la confirmation.

### Étape 6 — Charger les données de démonstration
```bash
php bin/console doctrine:fixtures:load
```
Répondre `yes`. Cela crée :
- Un compte **administrateur**
- 5 comptes **clients**
- 10 **crypto-monnaies** avec leur historique de prix

### Étape 7 — Installer les assets (JS/CSS)
```bash
php bin/console importmap:install
```

---

## Lancer le projet

```bash
symfony server:start
```

Ouvrir le navigateur sur : **http://127.0.0.1:8000**

> Pour arrêter le serveur : `symfony server:stop`

---

## Comptes de connexion (après fixtures)

| Rôle          | Email                        | Mot de passe  |
|---------------|------------------------------|---------------|
| Administrateur | admin@bitchest.com          | `admin123`    |
| Client 1      | jean.dupont@email.com        | `password123` |
| Client 2      | marie.martin@email.com       | `password123` |
| Client 3      | pierre.durand@email.com      | `password123` |
| Client 4      | sophie.bernard@email.com     | `password123` |
| Client 5      | thomas.petit@email.com       | `password123` |

---

## Résumé des commandes

```bash
# 1. Cloner
git clone https://github.com/Raoul-Ekwa/bitChest.git && cd bitChest

# 2. Dépendances
composer install

# 3. Configurer .env.local avec votre DATABASE_URL

# 4. Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load

# 5. Assets
php bin/console importmap:install

# 6. Lancer
symfony server:start
```

---

## Générer des cotations de prix

```bash
php bin/console app:generate-quotes
```

---

## Lancer les tests

```bash
# Créer la base de données de test (une seule fois)
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test

# Lancer tous les tests
php bin/phpunit
```

---

## Structure du projet

```
bitChest/
├── src/
│   ├── Controller/
│   │   ├── Admin/          # Interface administrateur
│   │   ├── Client/         # Interface client
│   │   └── SecurityController.php
│   ├── Entity/             # Entités Doctrine (User, Crypto, Wallet...)
│   ├── Form/               # Formulaires Symfony
│   ├── Service/            # Logique métier
│   └── DataFixtures/       # Données de démonstration
├── templates/              # Vues Twig
├── migrations/             # Scripts SQL de migration
├── tests/                  # Tests unitaires et fonctionnels
├── .env                    # Configuration de base
└── .env.local              # ⚠️ À créer — configuration locale (non commité)
```

---

## Dépannage fréquent

**Erreur `php: command not found`**
→ PHP n'est pas dans le PATH. Vérifier l'étape d'installation PHP.

**Erreur `SQLSTATE[HY000] [1049] Unknown database`**
→ La base de données n'existe pas. Relancer `php bin/console doctrine:database:create`.

**Erreur `SQLSTATE[HY000] [1045] Access denied`**
→ Identifiants MySQL incorrects dans `.env.local`.

**Page blanche ou erreur 500**
→ Vider le cache : `php bin/console cache:clear`

**Assets non chargés (page sans style)**
→ Relancer : `php bin/console importmap:install`
