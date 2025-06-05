# 🚗 EcoRide – Plateforme de covoiturage éco-responsable

**EcoRide** est une application web dynamique de covoiturage, pensée pour favoriser les trajets partagés en mettant l'accent sur l'écologie.  
Développée avec **Symfony 7** et **MySQL**, elle permet aux utilisateurs de proposer ou réserver des trajets, noter les conducteurs, gérer leurs préférences, et plus encore.

---

## 🌱 Objectif

> Permettre aux utilisateurs de réduire leur empreinte carbone en partageant leurs trajets via une plateforme moderne, rapide et sécurisée.

---

## ⚙️ Stack technique

| Côté            | Technologie                       |
| --------------- | --------------------------------- |
| Langage         | PHP 8.3                           |
| Framework       | Symfony 7.2                       |
| Base de données | MySQL 8                           |
| Front-end       | HTML5, CSS3, Bootstrap, JS        |
| ORM             | Doctrine                          |
| Outils          | Composer, Symfony CLI, npm        |
| Déploiement     | Fly.io, Heroku, Vercel (au choix) |

---

## 📁 Structure du projet

```bash
EcoRide/
├── assets/              # Fichiers front-end
├── bin/                 # Console Symfony
├── config/              # Configuration de l'application
├── migrations/          # Fichiers de migration Doctrine
├── public/              # Point d'entrée (index.php)
├── src/                 # Code PHP (Controller, Entity, etc.)
├── templates/           # Vues Twig
├── tests/               # Tests unitaires
├── translations/        # Traductions
└── .env, composer.json, README.md, ...
```

---

## 🚀 Installation locale

### ✅ Prérequis

- PHP >= 8.1
- Composer
- Symfony CLI
- Node.js + npm
- MySQL ou MariaDB

### 📦 Étapes

```bash
# 1. Cloner le dépôt
git clone git@github.com:1-Victor/EcoRide.git
cd EcoRide

# 2. Installer les dépendances PHP
composer install

# 3. Configurer l'environnement
cp .env .env.local
# 👉 Modifier DATABASE_URL avec vos identifiants MySQL

# 4. Créer la base de données
php bin/console doctrine:database:create

# 5. Exécuter les migrations
php bin/console doctrine:migrations:migrate

# 6. Installer les dépendances front-end
npm install
npm run build

# 7. Lancer le serveur local
symfony server:start
```

---

## 🧑‍💻 Déploiement Fly.io (exemple)

### Installation de `flyctl`

```bash
curl -L https://fly.io/install.sh | sh
```

### Déploiement

```bash
fly launch
fly deploy
```

---

## 🔐 Fonctionnalités principales

- 🚘 Création et réservation de trajets
- 👥 Système de rôles (admin/utilisateur)
- 💬 Avis entre utilisateurs
- 📱 Interface responsive (Bootstrap)
- 🌱 Gestion des préférences écolo
- 🔐 Authentification sécurisée
- 🚗 Gestion des véhicules, marques, énergies

---

## ✅ Entités Symfony

- Entités générées via `make:entity`
- Relations Doctrine (OneToMany, ManyToOne, etc.)
- Migrations Doctrine automatisées
- Validations avec annotations

---

## 🧪 Lancer les tests

```bash
php bin/phpunit
```

---

## 📖 Bonnes pratiques

- Architecture MVC propre
- Respect PSR-12
- Variables d’environnement `.env` sécurisées
- Séparation claire front/back
- Validation Symfony + CSRF
- Versionnement Git propre

---

## 👤 Auteur

- **Développement principal** : Victor (poste local)
- **Hébergement & GitHub** : Victor ([github.com/1-Victor](https://github.com/1-Victor))
