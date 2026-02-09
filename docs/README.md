# BitChest - Documentation

## Structure des documents

```
docs/
├── README.md                           # Ce fichier (index)
├── conception/
│   └── 01-dossier-conception.md        # Dossier de conception complet
├── journal/
│   └── journal-developpement.md        # Journal de développement
├── tests/
│   └── plan-de-tests.md                # Plan de tests (OPTP)
└── deploiement/
    └── guide-deploiement.md            # Guide de déploiement
```

## Documents à rendre

### 1. Dossier de Conception
**Fichier** : `conception/01-dossier-conception.md`

Contient :
- Reformulation de la demande client
- Adresse GitHub
- Documents de conception (Zoning, Wireframes, Maquette Figma)
- Schéma d'enchaînement des écrans
- Liste fonctionnelle
- Schémas de la base de données (MCD, UML)
- Architecture technique
- Besoins d'éco-conception
- Stratégie de sécurité
- [À compléter] Captures d'écran

### 2. Journal de Développement
**Fichier** : `journal/journal-developpement.md`

Contient :
- Comptes rendus de développement (par semaine)
- Comptes rendus des Sprints (Planning, Review, Retrospective)
- Comptes rendus des tests utilisateurs
- Veille technologique

### 3. Plan de Tests
**Fichier** : `tests/plan-de-tests.md`

Contient :
- Objectifs des tests
- Périmètre (in/out scope)
- Types de tests (unitaires, fonctionnels, sécurité)
- Scénarios de test détaillés
- Critères d'acceptation
- Rapport de tests

### 4. Guide de Déploiement
**Fichier** : `deploiement/guide-deploiement.md`

Contient :
- Technologies requises
- Instructions d'installation
- Configuration serveur (Apache/Nginx)
- Configuration HTTPS
- Tâches planifiées (Cron)
- Dépannage

## Conversion en Word

Pour convertir les fichiers Markdown en Word, vous pouvez utiliser :

### Option 1 : Pandoc (recommandé)

```bash
# Installer Pandoc
# Windows: télécharger depuis https://pandoc.org/installing.html
# Linux: sudo apt install pandoc

# Convertir un fichier
pandoc conception/01-dossier-conception.md -o dossier-conception.docx

# Convertir tous les fichiers
pandoc conception/01-dossier-conception.md -o "Dossier de Conception.docx"
pandoc journal/journal-developpement.md -o "Journal de Développement.docx"
pandoc tests/plan-de-tests.md -o "Plan de Tests.docx"
pandoc deploiement/guide-deploiement.md -o "Guide de Déploiement.docx"
```

### Option 2 : Éditeurs en ligne

- [Dillinger](https://dillinger.io/) - Export Word
- [StackEdit](https://stackedit.io/) - Export Word
- [Markdown to Word](https://word.to/markdown-to-word)

### Option 3 : Visual Studio Code

1. Installer l'extension "Markdown All in One"
2. Ouvrir le fichier .md
3. Ctrl+Shift+P → "Markdown: Export to Word"

## Éléments à compléter

Les documents contiennent des sections à compléter marquées `[À compléter]` :

- [ ] Adresse GitHub du repository
- [ ] Lien vers maquette Figma publique
- [ ] Dates des sprints
- [ ] Noms des membres de l'équipe
- [ ] Captures d'écran de l'application
- [ ] Résultats des tests utilisateurs
- [ ] Comptes rendus des sprints réels
