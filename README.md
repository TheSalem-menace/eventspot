# 🎫 EventSpot — Plateforme d'Événements

Projet Symfony 7 — Application Complète de Gestion d'Événements | 2026

## 📋 Description

EventSpot est une plateforme complète de gestion et de découverte d'événements permettant aux utilisateurs de s'inscrire à des événements, aux organisateurs de les créer et gérer, et aux administrateurs d'administrer la plateforme.

### 🌟 Fonctionnalités principales

- **📋 CRUD complet** pour les événements avec validation avancée
- **🏛️ Gestion des lieux** et des tags d'événements
- **👥 Système d'inscription** avec gestion des capacités
- **🔐 Authentification et rôles** (Admin, Organisateur, Utilisateur)
- **🔍 Recherche avancée** avec filtres multiples
- **📱 Interface responsive** avec Bootstrap 5
- **📧 Emails de confirmation** via Mailtrap
- **🖼️ Upload d'images** pour les événements
- **📊 Pagination** et statistiques
- **🔌 API REST** avec API Platform
- **🧪 Tests unitaires et fonctionnels**
- **📈 Commandes console** pour les rapports

## 🚀 Installation

### Prérequis
- PHP 8.2+
- Composer
- SQLite (configuré par défaut) ou MySQL 8.0+
- Symfony CLI (optionnel)
- Node.js (pour AssetMapper, optionnel)

### Étapes

```bash
# 1. Cloner le dépôt
git clone <url-du-depot> eventspot
cd eventspot

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env .env.local
# Modifier DATABASE_URL et MAILER_DSN dans .env.local si nécessaire

# 4. Créer la base de données et charger les données
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# 5. Lancer le serveur de développement
symfony server:start --port=8000 --no-tls
# ou
php -S localhost:8000 -t public/

# 6. Accéder à l'application
# http://localhost:8000
```

## ⚙️ Configuration Mailtrap

1. Créer un compte sur [mailtrap.io](https://mailtrap.io)
2. Aller dans Email Testing → Sandbox → votre inbox → SMTP Settings
3. Copier les identifiants dans `.env.local` :

```
MAILER_DSN=smtp://USERNAME:PASSWORD@sandbox.smtp.mailtrap.io:2525?encryption=tls&auth_mode=login
```

## 🔑 Identifiants de test

| Rôle          | Email                        | Mot de passe |
|---------------|------------------------------|--------------|
| Admin         | admin@eventspot.com          | admin123     |
| Organisateur  | orga1@eventspot.com          | orga123      |
| Organisateur  | orga2@eventspot.com          | orga123      |
| Utilisateur   | (5 comptes générés par Faker)| user123      |

### 🎯 Tests rapides

1. **Admin** : Accès complet à toutes les fonctionnalités
2. **Organisateur** : Peut créer/modifier/supprimer ses événements
3. **Utilisateur** : Peut s'inscrire aux événements et les consulter

## 🗄️ Schéma des relations

```
User (1) ──── (N) Evenement      [organisateur]
User (1) ──── (N) Inscription    [participant]
Evenement (1) ── (N) Inscription
Evenement (N) ── (N) TagEvenement
Evenement (N) ── (1) Lieu
```

## 📊 Données de fixtures

- **5** Lieux (Centre de congrès, Salle polyvalente, Amphithéâtre universitaire, Espace coworking, Parc municipal)
- **8** Tags (Networking, Tech, Gratuit, Startup, Formation, Culture, Sport, Famille) avec couleurs personnalisées
- **15** Événements variés (conférences, ateliers, meetups, formations, concerts) avec dates réalistes
- **8** Utilisateurs (1 admin, 2 organisateurs, 5 utilisateurs générés par Faker)
- **30** Inscriptions avec statuts variés (confirmée, en_attente, annulée)

```bash
# Recharger toutes les fixtures
php bin/console doctrine:fixtures:load --no-interaction

# Charger uniquement les événements
php bin/console doctrine:fixtures:load --group=EvenementFixture
```

## 🧪 Tests

L'application inclut une suite complète de tests unitaires et fonctionnels :

```bash
# Tous les tests
php bin/phpunit

# Tests unitaires seulement
php bin/phpunit tests/Service/

# Tests fonctionnels
php bin/phpunit tests/Controller/

# Tests API
php bin/phpunit tests/Api/

# Tests avec coverage
php bin/phpunit --coverage-html coverage
```

### 📈 Résultats des tests
- **16 tests** au total
- **19 assertions** 
- Tests des services, contrôleurs, et API
- Configuration de base de données de test séparée

## 📦 Commandes disponibles

### Commande de rapport EventSpot
```bash
# Rapport global avec statistiques complètes
php bin/console app:eventspot:report

# Prochains événements uniquement
php bin/console app:eventspot:report --upcoming

# Événements par lieu (par ID)
php bin/console app:eventspot:report --lieu=1

# Événements par lieu (par nom)
php bin/console app:eventspot:report --lieu="Centre de congrès"
```

### Autres commandes utiles
```bash
# Voir les routes
php bin/console debug:router

# Vider le cache
php bin/console cache:clear

# Valider le schéma
php bin/console doctrine:schema:validate

# Générer une nouvelle migration
php bin/console make:migration
```

## 🏗️ Architecture

```
src/
├── Command/          # EventSpotReportCommand (rapports et statistiques)
├── Controller/       # EvenementController, HomeController, SecurityController, ...
├── DataFixtures/     # UserFixtures, LieuFixtures, TagEvenementFixtures, EvenementFixture, ...
├── Entity/           # Evenement, Lieu, TagEvenement, Inscription, User
├── EventSubscriber/  # EventSpotSubscriber (X-EventSpot-Version header)
├── Form/             # EvenementType, InscriptionType, TagType, RegistrationFormType
├── Repository/       # EvenementRepository (findUpcoming, findByFilters), ...
├── Service/          # EvenementManager, FileUploader
└── Twig/             # EventSpotExtension (time_ago, price_format, capacity_badge)
```

## 🌐 API Platform

API REST complète disponible sur `/api` avec authentification par rôles :

### Endpoints Événements
- `GET /api/evenements` — Liste des événements (lecture seule)
- `GET /api/evenements/{id}` — Détail d'un événement
- `POST /api/evenements` — Créer un événement (ROLE_ORGANISATEUR)
- `PUT /api/evenements/{id}` — Modifier un événement (ROLE_ORGANISATEUR)
- `DELETE /api/evenements/{id}` — Supprimer un événement (ROLE_ADMIN)

### Endpoints Lieux
- `GET /api/lieux` — Liste des lieux
- `GET /api/lieux/{id}` — Détail d'un lieu

### Groupes de sérialisation
- `event:read` — Données publiques (titre, description, dates, lieu, catégorie, statut, prix, tags)
- `event:write` — Données d'écriture (titre, description, dates, catégorie, capacité, prix)

### Documentation
Accédez à **Swagger UI** : `http://localhost:8000/api/docs`

## 🎨 Fonctionnalités détaillées

### 📋 Gestion des événements
- **CRUD complet** avec validation avancée
- **Upload d'images** (JPEG/PNG/WebP, max 2Mo)
- **Gestion des tags** (sélection multiple avec couleurs)
- **Statuts automatiques** (brouillon, publié, complet, annulé)
- **Capacité et inscription** avec jauge de remplissage

### 🔍 Recherche et filtrage
- **Recherche par titre** (recherche partielle)
- **Filtre par catégorie** (conférence, atelier, meetup, formation, concert)
- **Filtre par ville** du lieu
- **Filtre par tag** (multi-sélection)
- **Pagination** (9 événements par page)

### 👥 Système d'inscription
- **Inscription avec commentaire** optionnel
- **Gestion des capacités** (places restantes, complet)
- **Statuts d'inscription** (confirmée, en attente, annulée)
- **Email de confirmation** automatique
- **Historique des événements consultés** (session)

### 🔐 Sécurité et rôles
- **Hiérarchie des rôles** : ROLE_ORGANISATEUR → ROLE_ADMIN
- **Contrôle d'accès** par `#[IsGranted]`
- **Navigation conditionnelle** selon le rôle
- **Organisateur automatique** sur création d'événement

### 📧 Emails
- **Confirmation d'inscription** avec template HTML
- **Configuration Mailtrap** pour le développement
- **Template personnalisé** avec détails de l'événement

### 🎯 Extensions Twig
- `time_ago` — Format relatif des dates ("il y a 3 jours")
- `price_format` — Formatage des prix ("Gratuit 🎉" ou "15,50 €")
- `capacity_badge` — Badge de remplissage avec couleurs

## 📱 Interface utilisateur

### Design responsive
- **Bootstrap 5** avec thème personnalisé
- **Navbar avec navigation conditionnelle**
- **Cards d'événements** avec effets hover
- **Badges colorés** pour catégories et statuts
- **Barres de progression** pour le remplissage
- **Alertes flash** après chaque action

### Pages principales
- **Accueil** — 6 prochains événements + derniers consultés
- **Liste des événements** — Recherche, filtres, pagination
- **Détail événement** — Informations complètes + inscription
- **Création/Modification** — Formulaire complet avec tags et image
- **Gestion des lieux et tags** — CRUD pour les administrateurs

## 🔧 Configuration technique

### Base de données
- **SQLite** par défaut (facilement configurable pour MySQL/PostgreSQL)
- **Migrations Doctrine** pour le versioning
- **Fixtures avec FakerPHP** pour les données de test
- **Relations optimisées** avec index appropriés

### Performance
- **Pagination KnpPaginator** pour les grandes listes
- **QueryBuilder optimisé** pour la recherche
- **Cache Symfony** configuré pour la production
- **AssetMapper** pour les assets frontend

### Tests
- **PHPUnit** avec configuration séparée
- **Tests unitaires** pour les services
- **Tests fonctionnels** pour les contrôleurs
- **Tests API** pour les endpoints REST
- **Base de données de test** isolée

## 🚀 Déploiement

### Production
```bash
# Variables d'environnement recommandées
APP_ENV=prod
APP_DEBUG=false
DATABASE_URL=postgresql://user:pass@host:5432/dbname
MAILER_DSN=smtp://user:pass@host:587?encryption=tls

# Optimisations
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --env=prod
```

### Docker (optionnel)
```dockerfile
FROM php:8.2-fpm
# ... configuration Docker
```

## 🤝 Contributing

### Structure du code
- Respecter les **normes PSR-12**
- Utiliser les **types stricts**
- Ajouter des **tests** pour nouvelles fonctionnalités
- Documenter les **méthodes publiques**

### Git workflow
```bash
# Branches
git checkout -b feature/nouvelle-fonctionnalite
git commit -m "feat: ajouter la recherche par tags"
git push origin feature/nouvelle-fonctionnalite
```

## 📝 License

Projet éducatif Symfony 7 — 2026

---

**🎉 EventSpot est prêt !** 

Lancez `symfony server:start` et commencez à créer et gérer vos événements !
