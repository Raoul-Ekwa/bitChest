# BitChest - Guide de Déploiement

## 1. Technologies requises

### Serveur

| Technologie | Version minimale | Recommandée |
|-------------|------------------|-------------|
| PHP | 8.2 | 8.3 |
| MySQL | 8.0 | 8.0 |
| Composer | 2.x | 2.6+ |
| Node.js | 18.x | 20.x |
| npm | 9.x | 10.x |

### Extensions PHP requises

```
- php-mysql (ou php-pdo_mysql)
- php-xml
- php-ctype
- php-iconv
- php-intl
- php-mbstring
- php-bcmath
- php-curl
```

### Vérification des prérequis

```bash
# Vérifier version PHP
php -v

# Vérifier extensions PHP
php -m | grep -E "(mysql|xml|ctype|iconv|intl|mbstring|bcmath|curl)"

# Vérifier Composer
composer --version

# Vérifier MySQL
mysql --version

# Vérifier Node.js et npm
node -v
npm -v
```

---

## 2. Installation depuis le code source

### 2.1 Cloner le repository

```bash
# Cloner le projet
git clone [URL_REPOSITORY] bitchest
cd bitchest
```

### 2.2 Installer les dépendances PHP

```bash
# Installation des dépendances via Composer
composer install --no-dev --optimize-autoloader
```

### 2.3 Installer les dépendances Frontend

```bash
# Installation des dépendances npm
npm install

# Compilation des assets pour production
npm run build
```

### 2.4 Configuration de l'environnement

```bash
# Copier le fichier d'environnement
cp .env .env.local

# Éditer le fichier avec vos paramètres
nano .env.local
```

**Contenu du fichier `.env.local` :**

```env
# Application
APP_ENV=prod
APP_SECRET=VOTRE_SECRET_UNIQUE_32_CARACTERES

# Base de données
DATABASE_URL="mysql://utilisateur:mot_de_passe@127.0.0.1:3306/bitchest?serverVersion=8.0"
```

> **Important** : Générer un APP_SECRET unique avec :
> ```bash
> php -r "echo bin2hex(random_bytes(16));"
> ```

### 2.5 Créer la base de données

```bash
# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Charger les données initiales (cryptos + admin)
php bin/console doctrine:fixtures:load --no-interaction
```

### 2.6 Générer les cotations

```bash
# Générer 30 jours d'historique
php bin/console app:generate-quotes --days=30
```

### 2.7 Vider le cache

```bash
# Vider et préchauffer le cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

---

## 3. Configuration du serveur web

### 3.1 Apache

Créer un fichier `.htaccess` dans le dossier `public/` (déjà inclus) ou configurer un VirtualHost :

```apache
<VirtualHost *:80>
    ServerName bitchest.local
    DocumentRoot /var/www/bitchest/public

    <Directory /var/www/bitchest/public>
        AllowOverride All
        Require all granted
        FallbackResource /index.php
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/bitchest_error.log
    CustomLog ${APACHE_LOG_DIR}/bitchest_access.log combined
</VirtualHost>
```

Activer le site et les modules nécessaires :

```bash
sudo a2enmod rewrite
sudo a2ensite bitchest.conf
sudo systemctl reload apache2
```

### 3.2 Nginx

```nginx
server {
    listen 80;
    server_name bitchest.local;
    root /var/www/bitchest/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/bitchest_error.log;
    access_log /var/log/nginx/bitchest_access.log;
}
```

---

## 4. Configuration HTTPS (Production)

### 4.1 Avec Let's Encrypt (Certbot)

```bash
# Installer Certbot
sudo apt install certbot python3-certbot-apache  # Pour Apache
# ou
sudo apt install certbot python3-certbot-nginx   # Pour Nginx

# Obtenir le certificat
sudo certbot --apache -d bitchest.votredomaine.com
# ou
sudo certbot --nginx -d bitchest.votredomaine.com
```

---

## 5. Permissions des fichiers

```bash
# Définir le propriétaire (www-data pour Apache/Nginx)
sudo chown -R www-data:www-data /var/www/bitchest

# Permissions des dossiers
sudo find /var/www/bitchest -type d -exec chmod 755 {} \;

# Permissions des fichiers
sudo find /var/www/bitchest -type f -exec chmod 644 {} \;

# Dossiers avec écriture nécessaire
sudo chmod -R 775 /var/www/bitchest/var
sudo chmod -R 775 /var/www/bitchest/public/uploads  # Si uploads
```

---

## 6. Tâches planifiées (Cron)

Pour générer les cotations quotidiennes automatiquement :

```bash
# Éditer le crontab
crontab -e

# Ajouter la ligne suivante (exécution tous les jours à 00:05)
5 0 * * * cd /var/www/bitchest && php bin/console app:generate-quotes --daily >> /var/log/bitchest-quotes.log 2>&1
```

---

## 7. Comptes par défaut

Après chargement des fixtures, les comptes suivants sont disponibles :

### Administrateur

| Champ | Valeur |
|-------|--------|
| Email | admin@bitchest.com |
| Mot de passe | admin123 |

### Client de test

| Champ | Valeur |
|-------|--------|
| Email | client@bitchest.com |
| Mot de passe | client123 |

> **Important** : Changer ces mots de passe en production !

---

## 8. Vérification de l'installation

### Checklist de vérification

- [ ] Page de login accessible : `https://votredomaine.com/login`
- [ ] Connexion admin fonctionnelle
- [ ] Liste des 10 crypto-monnaies affichée
- [ ] Graphiques d'évolution visibles
- [ ] Création de client avec wallet 500€
- [ ] Connexion client fonctionnelle
- [ ] Achat/vente de crypto fonctionnels

### Commandes de diagnostic

```bash
# Vérifier la configuration Symfony
php bin/console debug:config

# Vérifier les routes
php bin/console debug:router

# Vérifier la connexion BDD
php bin/console doctrine:schema:validate

# Vérifier les services
php bin/console debug:container --show-private
```

---

## 9. Dépannage

### Problèmes courants

| Problème | Solution |
|----------|----------|
| Page blanche | Vérifier les logs : `var/log/prod.log` |
| Erreur 500 | Vérifier permissions dossier `var/` |
| BDD inaccessible | Vérifier DATABASE_URL dans `.env.local` |
| Assets non chargés | Exécuter `npm run build` |
| Cache problème | `php bin/console cache:clear --env=prod` |

### Logs utiles

```bash
# Logs Symfony
tail -f var/log/prod.log

# Logs Apache
tail -f /var/log/apache2/bitchest_error.log

# Logs Nginx
tail -f /var/log/nginx/bitchest_error.log

# Logs MySQL
tail -f /var/log/mysql/error.log
```

---

## 10. Sauvegarde et restauration

### Sauvegarde base de données

```bash
# Créer une sauvegarde
mysqldump -u utilisateur -p bitchest > backup_bitchest_$(date +%Y%m%d).sql

# Compresser
gzip backup_bitchest_*.sql
```

### Restauration

```bash
# Décompresser si nécessaire
gunzip backup_bitchest_20240101.sql.gz

# Restaurer
mysql -u utilisateur -p bitchest < backup_bitchest_20240101.sql
```

---

## 11. Mise à jour de l'application

```bash
# Passer en mode maintenance (optionnel)
# Créer un fichier maintenance.html dans public/

# Récupérer les dernières modifications
git pull origin main

# Mettre à jour les dépendances
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Vider le cache
php bin/console cache:clear --env=prod

# Retirer le mode maintenance
```

---

## 12. Résumé des commandes

```bash
# === INSTALLATION COMPLÈTE ===

# 1. Cloner
git clone [URL] bitchest && cd bitchest

# 2. Dépendances
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. Configuration
cp .env .env.local
# Éditer .env.local avec vos paramètres

# 4. Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# 5. Cotations
php bin/console app:generate-quotes --days=30

# 6. Cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 7. Permissions (Linux)
sudo chown -R www-data:www-data .
sudo chmod -R 775 var/
```

---

## Contact Support

Pour toute question concernant le déploiement :
- **Email** : [À compléter]
- **Documentation** : [Lien vers documentation]
