# BitChest - Journal de Développement

## Informations Projet

- **Projet** : BitChest - Plateforme d'échange de crypto-monnaies
- **Équipe** : [Noms des membres de l'équipe]
- **Date de début** : [Date]
- **Date de fin prévue** : [Date]

---

## Comptes Rendus de Développement

### Semaine 1 : Mise en place du projet

#### Date : [À compléter]

**Tâches réalisées :**
- [x] Initialisation du projet Symfony 7
- [x] Configuration de la base de données MySQL
- [x] Mise en place de l'architecture des dossiers
- [x] Configuration de Git et création du repository

**Difficultés rencontrées :**
| Problème | Solution apportée |
|----------|-------------------|
| Configuration Doctrine | Ajustement des paramètres dans .env |
| [À compléter] | [À compléter] |

**Prochaines étapes :**
- Création des entités
- Mise en place de l'authentification

---

### Semaine 2 : Développement des entités et authentification

#### Date : [À compléter]

**Tâches réalisées :**
- [x] Création des entités (User, Client, Administrator, Wallet, etc.)
- [x] Mise en place de l'héritage Doctrine (JOINED)
- [x] Configuration de Symfony Security
- [x] Création du système d'authentification

**Difficultés rencontrées :**
| Problème | Solution apportée |
|----------|-------------------|
| Héritage Doctrine JOINED | Configuration du discriminator column |
| Requêtes sur entités héritées | Création de ClientRepository dédié |
| [À compléter] | [À compléter] |

**Prochaines étapes :**
- Développement des interfaces admin
- Système de gestion des clients

---

### Semaine 3 : Interface Administration

#### Date : [À compléter]

**Tâches réalisées :**
- [x] Dashboard administrateur
- [x] CRUD complet des clients
- [x] Génération de mots de passe temporaires
- [x] Affichage des crypto-monnaies

**Difficultés rencontrées :**
| Problème | Solution apportée |
|----------|-------------------|
| Affichage clients (JOINED inheritance) | Utilisation de ClientRepository au lieu de UserRepository |
| [À compléter] | [À compléter] |

**Prochaines étapes :**
- Interface client
- Système de portefeuille

---

### Semaine 4 : Interface Client et Portefeuille

#### Date : [À compléter]

**Tâches réalisées :**
- [x] Dashboard client avec statistiques
- [x] Affichage du portefeuille
- [x] Système d'achat de crypto-monnaies
- [x] Système de vente de crypto-monnaies
- [x] Calculs de plus-values

**Difficultés rencontrées :**
| Problème | Solution apportée |
|----------|-------------------|
| Calcul du prix moyen d'achat | Implémentation dans CalculationService |
| Précision des calculs décimaux | Utilisation de bcmath pour les opérations |
| [À compléter] | [À compléter] |

**Prochaines étapes :**
- Graphiques d'évolution
- Génération des cotations

---

### Semaine 5 : Cotations et Graphiques

#### Date : [À compléter]

**Tâches réalisées :**
- [x] Service de génération des cotations
- [x] Commande Symfony pour génération automatique
- [x] Intégration Chart.js
- [x] Graphiques d'évolution sur 30 jours

**Difficultés rencontrées :**
| Problème | Solution apportée |
|----------|-------------------|
| Algorithme de cotation | Implémentation basée sur cotation_generator.php |
| Prix négatifs | Ajout d'une valeur minimum (0.00000001) |
| [À compléter] | [À compléter] |

**Prochaines étapes :**
- Tests unitaires
- Finalisation interface

---

### Semaine 6 : Tests et Finalisation

#### Date : [À compléter]

**Tâches réalisées :**
- [x] Tests unitaires (CalculationService, WalletService)
- [x] Tests fonctionnels (TransactionController)
- [x] Responsive design
- [x] Traduction en anglais

**Difficultés rencontrées :**
| Problème | Solution apportée |
|----------|-------------------|
| [À compléter] | [À compléter] |

**Prochaines étapes :**
- Documentation
- Déploiement

---

## Comptes Rendus des Sprints

### Sprint 1 : Setup & Authentification

**Période** : [Date début] - [Date fin]

**Sprint Planning :**
- Configuration projet Symfony
- Base de données et entités
- Système d'authentification

**Sprint Review :**
| User Story | Statut | Commentaires |
|------------|--------|--------------|
| Configuration projet | ✅ Terminé | |
| Entités Doctrine | ✅ Terminé | Héritage JOINED configuré |
| Authentification | ✅ Terminé | Login/Logout fonctionnels |

**Sprint Retrospective :**
- **Ce qui a bien fonctionné** : [À compléter]
- **Ce qui peut être amélioré** : [À compléter]
- **Actions pour le prochain sprint** : [À compléter]

---

### Sprint 2 : Administration

**Période** : [Date début] - [Date fin]

**Sprint Planning :**
- Dashboard admin
- CRUD clients
- Gestion crypto-monnaies

**Sprint Review :**
| User Story | Statut | Commentaires |
|------------|--------|--------------|
| Dashboard admin | ✅ Terminé | |
| Création client | ✅ Terminé | Mot de passe temporaire généré |
| Liste clients | ✅ Terminé | Bug corrigé (ClientRepository) |
| Modification client | ✅ Terminé | |
| Suppression client | ✅ Terminé | |

**Sprint Retrospective :**
- **Ce qui a bien fonctionné** : [À compléter]
- **Ce qui peut être amélioré** : [À compléter]
- **Actions pour le prochain sprint** : [À compléter]

---

### Sprint 3 : Espace Client

**Période** : [Date début] - [Date fin]

**Sprint Planning :**
- Dashboard client
- Portefeuille
- Achat/Vente crypto

**Sprint Review :**
| User Story | Statut | Commentaires |
|------------|--------|--------------|
| Dashboard client | ✅ Terminé | Solde toujours visible |
| Portefeuille | ✅ Terminé | Holdings + transactions |
| Achat crypto | ✅ Terminé | |
| Vente crypto | ✅ Terminé | Récupération EUR |
| Graphiques | ✅ Terminé | Chart.js intégré |

**Sprint Retrospective :**
- **Ce qui a bien fonctionné** : [À compléter]
- **Ce qui peut être amélioré** : [À compléter]
- **Actions pour le prochain sprint** : [À compléter]

---

### Sprint 4 : Finalisation

**Période** : [Date début] - [Date fin]

**Sprint Planning :**
- Tests
- Documentation
- Déploiement

**Sprint Review :**
| User Story | Statut | Commentaires |
|------------|--------|--------------|
| Tests unitaires | ✅ Terminé | |
| Tests fonctionnels | ✅ Terminé | |
| Documentation | ✅ Terminé | |
| Déploiement | ✅ Terminé | |

**Sprint Retrospective :**
- **Ce qui a bien fonctionné** : [À compléter]
- **Ce qui peut être amélioré** : [À compléter]
- **Leçons apprises** : [À compléter]

---

## Comptes Rendus des Tests Utilisateurs

### Session de test 1

**Date** : [À compléter]
**Testeur** : [Nom/Prénom]
**Profil** : [Étudiant de la classe]

**Scénarios testés :**

| Scénario | Résultat | Commentaires |
|----------|----------|--------------|
| Inscription nouveau compte | ✅ Réussi | |
| Connexion | ✅ Réussi | |
| Consultation portefeuille | ✅ Réussi | |
| Achat de Bitcoin | ✅ Réussi | |
| Vente de crypto | ✅ Réussi | |

**Retours utilisateur :**
- [À compléter]

**Améliorations suggérées :**
- [À compléter]

---

### Session de test 2

**Date** : [À compléter]
**Testeur** : [Nom/Prénom]
**Profil** : [Étudiant de la classe]

**Scénarios testés :**

| Scénario | Résultat | Commentaires |
|----------|----------|--------------|
| [À compléter] | | |

**Retours utilisateur :**
- [À compléter]

**Améliorations suggérées :**
- [À compléter]

---

## Veille Technologique

### Ressources consultées

| Date | Sujet | Source | Utilité |
|------|-------|--------|---------|
| [Date] | Symfony Security | symfony.com | Configuration authentification |
| [Date] | Doctrine Inheritance | doctrine-project.org | Héritage JOINED |
| [Date] | Chart.js | chartjs.org | Graphiques crypto |
| [Date] | Bootstrap 5 | getbootstrap.com | Interface responsive |
| [À compléter] | | | |

### Problèmes résolus via recherche

| Problème | Solution trouvée | Source |
|----------|------------------|--------|
| Requêtes sur entités héritées Doctrine | Créer un repository dédié pour chaque entité enfant | Stack Overflow |
| Calculs décimaux précis en PHP | Utiliser bcmath au lieu des floats | PHP Documentation |
| [À compléter] | | |
