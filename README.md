# 🚗 EcoRide – Plateforme de covoiturage éco-responsable

**EcoRide** est une application web dynamique de covoiturage, pensée pour favoriser les trajets partagés en mettant l'accent sur l'écologie.  
Développée avec **Symfony 7.2** et **MySQL**, elle permet aux utilisateurs de proposer ou réserver des trajets, noter les conducteurs, gérer leurs véhicules, définir leurs préférences et suivre leurs covoiturages.

---

## 🌱 Objectif

> Permettre aux utilisateurs de réduire leur empreinte carbone en partageant leurs trajets via une plateforme moderne, rapide, responsive et sécurisée.

---

## ⚙️ Stack technique

| Côté            | Technologie                       |
| --------------- | --------------------------------- |
| Langage         | PHP 8.3                           |
| Framework       | Symfony 7.2 (CLI)                 |
| Base de données | MySQL 8                           |
| Front-end       | HTML5, CSS3, Bootstrap, JS        |
| ORM             | Doctrine                          |
| Outils          | Composer, npm, Webpack Encore     |
| Déploiement     | Fly.io (production), Symfony CLI (local) |

---

## 📁 Structure du projet

```bash
EcoRide/
├── assets/              # JS/CSS front-end avec Webpack Encore
├── bin/                 # Console Symfony
├── config/              # Fichiers de configuration YAML
├── migrations/          # Migrations Doctrine (automatisées)
├── public/              # Point d'entrée HTTP (index.php)
├── src/                 # Code source PHP : Controller, Entity, Form, Repository, Security...
├── templates/           # Vues Twig organisées par dossier
├── tests/               # Tests unitaires et fonctionnels
├── translations/        # Fichiers de traduction i18n
└── .env, composer.json, webpack.config.js, ...
```

---

## 🚀 Installation locale (environnement de travail)

### ✅ Prérequis

- PHP ≥ 8.3
- Composer (PHP)
- Symfony CLI
- Node.js + npm
- MySQL
- Navigateur web

### 🔧 Étapes de mise en place

```bash
# 1. Cloner le dépôt Git
git clone git@github.com:1-Victor/EcoRide.git
cd EcoRide

# 2. Installer les dépendances PHP
composer install

# 3. Configurer les variables d’environnement
cp .env .env.local
# → Modifier DATABASE_URL avec vos identifiants MySQL locaux

# 4. Créer la base de données locale
php bin/console doctrine:database:create

# 5. Appliquer les migrations
php bin/console doctrine:migrations:migrate

# 6. Installer les dépendances front-end (JS/CSS)
npm install
npm run build

# 7. Démarrer le serveur local
symfony server:start
```

---

## 🔐 Sécurité

- Hashage des mots de passe avec `UserPasswordHasherInterface`
- Protection CSRF sur tous les formulaires via Symfony
- Contrôle des accès via les rôles utilisateurs (`ROLE_USER`, `ROLE_ADMIN`)
- Validation des données en front (`required`, `type=email`, etc.)
- Validation back-end avec contraintes Symfony (`@Assert`)
- Routes sécurisées via `security.yaml` et annotations

---

## 🧑‍💻 Déploiement Fly.io (exemple réel utilisé)

### Installer Fly CLI

```bash
curl -L https://fly.io/install.sh | sh
```

### Déploiement d’un conteneur Symfony

```bash
fly launch
fly deploy
```

L’application est alors disponible publiquement via une URL générée par Fly.io.

---

## ✨ Fonctionnalités principales

- 🔐 Inscription / Connexion sécurisée
- 🚘 Création et réservation de trajets (date, heure, ville, prix, voiture)
- 👥 Gestion des rôles (Admin / Utilisateur)
- ✅ Validation des trajets en fin de parcours
- 💬 Système d’avis entre passagers et conducteurs
- 📱 Responsive design (Bootstrap)
- ⚙️ Tableau de bord personnel (profil, trajets, véhicules)
- 🌿 Filtre écologique : voiture électrique, Crit’Air
- 📊 Statistiques et interface admin (EasyAdmin ou perso)
- 🔎 Moteur de recherche des trajets + filtres (prix, durée, note...)

---

## 📌 Entités principales

- `User` : utilisateurs avec rôles, profil, préférences, crédits
- `CarSharings` : trajets avec lien passager/conducteur
- `Vehicle` : véhicules personnels (marque, modèle, plaques, etc.)
- `Review` : avis entre utilisateurs
- `PassengerConfirmation` : validation de trajet
- `CritAir`, `Energy`, `Transmission`, etc.

---

## 🧠 Bonnes pratiques respectées

- Architecture MVC (modulaire, claire)
- Respect de la norme PSR-12
- Git versionné avec messages clairs (branche main/dev)
- Sécurité : validation, CSRF, authentification par rôles
- Variables d’environnement `.env.local` sécurisées
- Code commenté, factorisé, routes nommées

---

## 👤 Auteur

- **Développement intégral** : Victor GIL
- **Formation** : Studi – TP Développeur Web & Web Mobile
- **GitHub** : [github.com/1-Victor](https://github.com/1-Victor)
- **Projet personnel et professionnalisant**
