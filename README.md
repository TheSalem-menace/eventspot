# 🎫 EventSpot — Plateforme d'Événements

Projet Symfony 7 — Mini-Projet 4 | 2026

## 📋 Description

EventSpot est une plateforme de gestion et de découverte d'événements permettant aux utilisateurs de s'inscrire à des événements, aux organisateurs de les créer et gérer, et aux administrateurs d'administrer la plateforme.

## 🚀 Installation

### Prérequis
- PHP 8.2+
- Composer
- MySQL 8.0+
- Symfony CLI (optionnel)

### Étapes

```bash
# 1. Cloner le dépôt
git clone <url-du-depot> eventspot
cd eventspot

# 2. Installer les dépendances
composer install

# 3. Copier et configurer l'environnement
cp .env .env.local
# Modifier DATABASE_URL et MAILER_DSN dans .env.local

# 4. Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Charger les fixtures
php bin/console doctrine:fixtures:load

# 6. Lancer le serveur
symfony serve
# ou
php -S localhost:8000 -t public/
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
| Admin         | admin@eventspot.fr           | admin123     |
| Organisateur  | organisateur0@eventspot.fr   | orga123      |
| Organisateur  | organisateur1@eventspot.fr   | orga123      |
| Utilisateur   | (emails générés par Faker)   | user123      |

## 🗄️ Schéma des relations

```
User (1) ──── (N) Evenement      [organisateur]
User (1) ──── (N) Inscription    [participant]
Evenement (1) ── (N) Inscription
Evenement (N) ── (N) TagEvenement
Evenement (N) ── (1) Lieu
```

## 📊 Données de fixtures

- **5** Lieux (Paris, Lyon x2, Bordeaux, Marseille)
- **8** Tags (Technologie, Formation, Réseau, Design, Marketing, Open Source, IA, Startups)
- **15** Événements (conférences, ateliers, meetups, formations, concerts)
- **8** Utilisateurs (1 admin, 2 organisateurs, 5 users)
- **30** Inscriptions

```bash
php bin/console doctrine:fixtures:load
```

## 🧪 Tests

```bash
# Tous les tests
php bin/phpunit

# Tests unitaires seulement
php bin/phpunit tests/Service/

# Tests fonctionnels
php bin/phpunit tests/Controller/
```

## 📦 Commandes disponibles

```bash
# Rapport global
php bin/console app:eventspot:report

# Prochains événements
php bin/console app:eventspot:report --upcoming

# Événements par lieu
php bin/console app:eventspot:report --lieu=1
```

## 🏗️ Architecture

```
src/
├── Command/          # app:eventspot:report
├── Controller/       # EvenementController, HomeController, SecurityController, ...
├── DataFixtures/     # UserFixtures, LieuFixtures, TagEvenementFixtures, ...
├── Entity/           # Evenement, Lieu, TagEvenement, Inscription, User
├── EventSubscriber/  # EventSpotSubscriber (X-EventSpot-Version header)
├── Form/             # EvenementType, InscriptionType, TagType, RegistrationFormType
├── Repository/       # findUpcoming(), findByFilters()
├── Service/          # EvenementManager
└── Twig/             # EventSpotExtension (time_ago, price_format, capacity_badge)
```

## 🌐 API Platform

API disponible sur `/api` :
- `GET /api/evenements` — Liste des événements
- `GET /api/evenements/{id}` — Détail d'un événement
- `GET /api/lieux` — Liste des lieux
- `GET /api/tag_evenements` — Liste des tags
