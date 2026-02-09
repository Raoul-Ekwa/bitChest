# BitChest - Plan de Tests (OPTP)

## One Page Test Plan

### Informations générales

| Élément | Détail |
|---------|--------|
| **Projet** | BitChest - Plateforme d'échange crypto |
| **Version** | 1.0 |
| **Date** | [À compléter] |
| **Auteur** | [À compléter] |

---

## 1. Objectifs des tests

- Valider les fonctionnalités métier (achat/vente crypto)
- Vérifier la sécurité de l'authentification
- S'assurer de l'intégrité des calculs financiers
- Tester la robustesse de l'application

---

## 2. Périmètre des tests

### In Scope (Inclus)
- [x] Authentification (login/logout/register)
- [x] Gestion des clients (CRUD admin)
- [x] Portefeuille (affichage, solde)
- [x] Transactions (achat/vente)
- [x] Calculs (prix moyen, plus-value)
- [x] Affichage des cryptos et cotations

### Out of Scope (Exclus)
- [ ] Tests de charge/performance
- [ ] Tests de pénétration avancés
- [ ] Compatibilité navigateurs anciens (IE11)

---

## 3. Environnement de test

| Composant | Configuration |
|-----------|---------------|
| **OS** | Windows 10/11, Linux |
| **PHP** | 8.2+ |
| **MySQL** | 8.0 |
| **Navigateurs** | Chrome, Firefox, Edge (dernières versions) |
| **Framework test** | PHPUnit |

---

## 4. Types de tests

### 4.1 Tests Unitaires

| ID | Composant | Description | Fichier |
|----|-----------|-------------|---------|
| TU01 | CalculationService | Calcul valeur portefeuille | `CalculationServiceTest.php` |
| TU02 | CalculationService | Calcul profit/perte | `CalculationServiceTest.php` |
| TU03 | CalculationService | Calcul prix moyen | `CalculationServiceTest.php` |
| TU04 | WalletService | Ajout au solde | `WalletServiceTest.php` |
| TU05 | WalletService | Soustraction du solde | `WalletServiceTest.php` |
| TU06 | User Entity | Rôles utilisateur | `UserTest.php` |

### 4.2 Tests Fonctionnels

| ID | Fonctionnalité | Scénario | Fichier |
|----|----------------|----------|---------|
| TF01 | Authentification | Login avec credentials valides | `SecurityControllerTest.php` |
| TF02 | Authentification | Login avec credentials invalides | `SecurityControllerTest.php` |
| TF03 | Transaction | Achat de crypto | `TransactionControllerTest.php` |
| TF04 | Transaction | Vente de crypto | `TransactionControllerTest.php` |
| TF05 | Transaction | Achat avec solde insuffisant | `TransactionControllerTest.php` |

### 4.3 Tests de Sécurité

| ID | Test | Description | Résultat attendu |
|----|------|-------------|------------------|
| TS01 | CSRF | Soumission formulaire sans token | Rejet (403) |
| TS02 | Accès non autorisé | Client accède à /admin | Redirection login |
| TS03 | Accès non autorisé | Admin accède wallet autre client | Erreur 403 |
| TS04 | Injection SQL | Caractères spéciaux dans formulaires | Échappement correct |
| TS05 | XSS | Script dans champs texte | Échappement HTML |

---

## 5. Scénarios de test détaillés

### ST01 : Inscription d'un nouveau client

**Préconditions** : Utilisateur non connecté

| Étape | Action | Résultat attendu |
|-------|--------|------------------|
| 1 | Accéder à /register | Formulaire affiché |
| 2 | Remplir email, nom, prénom | Champs acceptés |
| 3 | Remplir mot de passe (2x) | Validation OK |
| 4 | Soumettre formulaire | Compte créé |
| 5 | Vérifier BDD | Client avec wallet 500€ |

### ST02 : Connexion administrateur

**Préconditions** : Compte admin existant

| Étape | Action | Résultat attendu |
|-------|--------|------------------|
| 1 | Accéder à /login | Formulaire affiché |
| 2 | Entrer email admin | Champ accepté |
| 3 | Entrer mot de passe | Champ accepté |
| 4 | Cliquer "Sign In" | Authentification |
| 5 | Vérifier redirection | /admin/dashboard |

### ST03 : Création client par admin

**Préconditions** : Admin connecté

| Étape | Action | Résultat attendu |
|-------|--------|------------------|
| 1 | Accéder à /admin/clients/new | Formulaire affiché |
| 2 | Remplir informations client | Champs validés |
| 3 | Soumettre formulaire | Client créé |
| 4 | Vérifier flash message | Mot de passe temporaire affiché |
| 5 | Vérifier BDD | Client + Wallet 500€ |

### ST04 : Achat de crypto-monnaie

**Préconditions** : Client connecté, solde > 0

| Étape | Action | Résultat attendu |
|-------|--------|------------------|
| 1 | Accéder à liste cryptos | Cryptos affichées avec prix |
| 2 | Cliquer sur Bitcoin | Page détail + graphique |
| 3 | Cliquer "Buy" | Formulaire achat |
| 4 | Entrer quantité (ex: 0.01) | Total calculé |
| 5 | Confirmer achat | Transaction créée |
| 6 | Vérifier portefeuille | Holding BTC ajouté |
| 7 | Vérifier solde | Balance réduite du montant |

### ST05 : Vente de crypto-monnaie

**Préconditions** : Client connecté, possède crypto

| Étape | Action | Résultat attendu |
|-------|--------|------------------|
| 1 | Accéder au portefeuille | Holdings affichés |
| 2 | Sélectionner crypto à vendre | Formulaire vente |
| 3 | Entrer quantité | Total calculé au cours actuel |
| 4 | Confirmer vente | Transaction créée |
| 5 | Vérifier portefeuille | Quantité réduite |
| 6 | Vérifier solde | Balance augmentée |

### ST06 : Calcul plus-value

**Préconditions** : Client avec holdings

| Étape | Action | Résultat attendu |
|-------|--------|------------------|
| 1 | Accéder au dashboard | Stats affichées |
| 2 | Vérifier "Total Cost" | Somme des achats |
| 3 | Vérifier "Current Value" | Quantité × Prix actuel |
| 4 | Vérifier "Profit/Loss" | Value - Cost |

---

## 6. Critères d'acceptation

### Critères globaux
- [x] Tous les tests unitaires passent (100%)
- [x] Tous les tests fonctionnels passent (100%)
- [x] Aucune faille de sécurité critique
- [x] Application responsive (mobile/tablet/desktop)
- [x] Temps de réponse < 3s par page

### Critères par fonctionnalité

| Fonctionnalité | Critères |
|----------------|----------|
| Authentification | Login/logout fonctionnels, redirection correcte |
| CRUD Clients | Création avec wallet 500€, modification, suppression |
| Portefeuille | Solde toujours visible, calculs corrects |
| Transactions | Achat/vente fonctionnels, mise à jour solde |
| Cotations | 10 cryptos, 30 jours historique, graphiques |

---

## 7. Exécution des tests

### Commandes

```bash
# Exécuter tous les tests
php bin/phpunit

# Tests unitaires uniquement
php bin/phpunit tests/Unit

# Tests fonctionnels uniquement
php bin/phpunit tests/Functional

# Test spécifique
php bin/phpunit tests/Unit/Service/CalculationServiceTest.php

# Avec couverture de code
php bin/phpunit --coverage-html coverage/
```

### Résultats attendus

```
PHPUnit 10.x

......................                                            22 / 22 (100%)

Time: 00:02.345, Memory: 24.00 MB

OK (22 tests, 45 assertions)
```

---

## 8. Rapport de tests

### Résumé exécution

| Type | Total | Passés | Échoués | Taux |
|------|-------|--------|---------|------|
| Unitaires | [X] | [X] | 0 | 100% |
| Fonctionnels | [X] | [X] | 0 | 100% |
| Sécurité | [X] | [X] | 0 | 100% |

### Bugs identifiés et corrigés

| ID | Description | Sévérité | Statut |
|----|-------------|----------|--------|
| BUG001 | Clients admin non affichés (inheritance Doctrine) | Haute | ✅ Corrigé |
| BUG002 | [À compléter] | | |

---

## 9. Couverture de code

| Composant | Couverture |
|-----------|------------|
| Services | > 80% |
| Entities | > 70% |
| Controllers | > 60% |
| **Total** | > 70% |

---

## 10. Conclusion

### Validation
- [ ] Tous les tests passent
- [ ] Couverture de code satisfaisante
- [ ] Aucun bug critique ouvert
- [ ] Application prête pour déploiement

### Signatures

| Rôle | Nom | Date | Signature |
|------|-----|------|-----------|
| Développeur | | | |
| Testeur | | | |
| Scrum Master | | | |
