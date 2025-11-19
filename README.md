## ProConnect · API + Portail d'administration

Plateforme Laravel 12 / Sanctum exposant l'API mobile et une interface d'administration web en SPA Vanilla JavaScript + Tailwind CSS.
L'administrateur peut superviser utilisateurs, entreprises, publications, statistiques globales et inviter de nouveaux gestionnaires.

### Fonctionnalités principales

**API Mobile (Laravel + Sanctum)**
- ✅ Authentification sécurisée avec tokens Sanctum
- ✅ Gestion complète des profils utilisateurs (consultation et modification)
- ✅ CRUD complet des entreprises (création, lecture, modification, suppression)
- ✅ CRUD complet des publications (création, lecture, modification, suppression)
- ✅ Persistance de session côté mobile (flutter_secure_storage)
- ✅ Pull-to-refresh sur toutes les vues
- ✅ Design professionnel avec Google Fonts, animations et composants premium

**Portail d'Administration Web (`/admin`)**
- ✅ Interface SPA complète en Vanilla JavaScript + Tailwind CSS (aucun framework)
- ✅ Design moderne et raffiné avec dégradés et animations
- ✅ Authentification administrateur dédiée avec gestion de session
- ✅ Tableau de bord avec statistiques en temps réel
- ✅ Recherche instantanée d'utilisateurs, entreprises et publications
- ✅ Gestion des administrateurs (liste et création de nouveaux admins)
- ✅ Navigation fluide sans rechargement de page

**Documentation API**
- ✅ Documentation Swagger/OpenAPI complète et interactive
- ✅ Tous les endpoints documentés avec exemples de requêtes/réponses
- ✅ Interface Swagger UI accessible via `/docs`

### Prérequis

- **PHP** 8.4+ (testé avec 8.4.14)
- **Composer** 2.x
- **PostgreSQL** ou MySQL (PostgreSQL recommandé pour Render.com)
- **Laravel** 12.38.1

### Installation rapide

```bash
# 1. Cloner le repository
git clone <votre-repo>
cd backend

# 2. Copier et configurer l'environnement
cp .env.example .env

# 3. Modifier .env avec vos paramètres de base de données
# DB_CONNECTION=pgsql  (ou mysql)
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=proconnect
# DB_USERNAME=votre_user
# DB_PASSWORD=votre_password

# 4. Installer les dépendances
composer install

# 5. Générer la clé d'application
php artisan key:generate

# 6. Créer la base de données (si elle n'existe pas)
# Pour PostgreSQL: createdb proconnect
# Pour MySQL: mysql -e "CREATE DATABASE proconnect"

# 7. Lancer les migrations et seeders
php artisan migrate --seed

# Le seeder crée automatiquement un compte administrateur par défaut :
# Email: admin@proconnect.test
# Password: password123
```

### Lancer le serveur en développement

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

L'application sera accessible sur :

- **API mobile** : http://localhost:8000/api/...
- **Portail admin** : http://localhost:8000/admin
  **Identifiants par défaut** : `admin@proconnect.test` / `password`
- **Documentation Swagger** : http://localhost:8000/docs

**Note** : L'interface admin est une SPA en Vanilla JavaScript utilisant Tailwind CSS via CDN. Aucun build npm n'est nécessaire - tous les assets sont chargés directement depuis le fichier `public/admin/index.html`.

### Documentation API (Swagger/OpenAPI)

La documentation complète de l'API est accessible via Swagger UI à l'adresse `/docs`.

**Accès à la documentation :**
```
http://localhost:8000/docs        (développement)
https://votre-app.onrender.com/docs  (production)
```

**Configuration L5-Swagger :**
- Package utilisé : `darkaonline/l5-swagger`
- Annotations dans les contrôleurs : tous les endpoints sont documentés
- Génération de la documentation : `php artisan l5-swagger:generate`

**Authentification dans Swagger :**
1. Cliquer sur le bouton "Authorize" en haut à droite
2. Entrer le token Bearer obtenu après connexion
3. Format : `Bearer votre_token_ici`

**Types d'authentification :**
- **Utilisateurs mobiles** : Token Sanctum standard via `/api/login`
- **Administrateurs** : Token Sanctum avec ability `admin` via `/api/admin/login`

### Provision automatique de l'administrateur

Un administrateur est synchronisé automatiquement via la commande artisan `app:ensure-admin` (lancée au boot des conteneurs via `docker/start.sh`).  
Définis les variables suivantes (dans `.env`, Render, etc.) pour ajuster les informations créées :

```
ADMIN_NAME="Admin ProConnect"
ADMIN_EMAIL=admin@proconnect.test
ADMIN_PASSWORD=password
ADMIN_ADDRESS="Siège ProConnect"
ADMIN_ACCOUNT_TYPE=pro
```

Si `ADMIN_EMAIL` ou `ADMIN_PASSWORD` sont absents, la commande est ignorée. Modifie ces valeurs avant chaque déploiement pour provisionner automatiquement l'accès administrateur.

### Endpoints API

#### Authentification (Public)

| Méthode | URL | Description | Authentification |
| --- | --- | --- | --- |
| POST | `/api/register` | Inscription d'un nouvel utilisateur (privé/pro) | Non |
| POST | `/api/login` | Connexion utilisateur mobile | Non |

#### Profil Utilisateur (Mobile)

| Méthode | URL | Description | Authentification |
| --- | --- | --- | --- |
| POST | `/api/logout` | Déconnexion et révocation du token | Bearer Token |
| GET | `/api/profile` | Récupération du profil utilisateur | Bearer Token |
| PUT | `/api/profile` | Modification du profil utilisateur | Bearer Token |

#### Entreprises (Mobile)

| Méthode | URL | Description | Authentification |
| --- | --- | --- | --- |
| GET | `/api/companies` | Liste toutes les entreprises | Bearer Token |
| POST | `/api/companies` | Création d'une nouvelle entreprise | Bearer Token |
| PUT | `/api/companies/{id}` | Modification d'une entreprise | Bearer Token |
| DELETE | `/api/companies/{id}` | Suppression d'une entreprise | Bearer Token |

#### Publications (Mobile)

| Méthode | URL | Description | Authentification |
| --- | --- | --- | --- |
| GET | `/api/posts` | Liste toutes les publications | Bearer Token |
| POST | `/api/posts` | Création d'une nouvelle publication | Bearer Token |
| PUT | `/api/posts/{id}` | Modification d'une publication | Bearer Token |
| DELETE | `/api/posts/{id}` | Suppression d'une publication | Bearer Token |

#### Administration

| Méthode | URL | Description | Authentification |
| --- | --- | --- | --- |
| POST | `/api/admin/login` | Connexion administrateur | Non |
| POST | `/api/admin/logout` | Déconnexion admin | Bearer Token (admin) |
| GET | `/api/admin/me` | Profil de l'admin connecté | Bearer Token (admin) |
| GET | `/api/admin/stats` | Statistiques globales (totaux + récents) | Bearer Token (admin) |
| GET | `/api/admin/users` | Recherche d'utilisateurs (param: `search`) | Bearer Token (admin) |
| GET | `/api/admin/companies` | Recherche d'entreprises (param: `search`) | Bearer Token (admin) |
| GET | `/api/admin/posts` | Recherche de publications (param: `search`) | Bearer Token (admin) |
| GET | `/api/admin/admins` | Liste tous les administrateurs | Bearer Token (admin) |
| POST | `/api/admin/admins` | Création d'un nouvel administrateur | Bearer Token (admin) |

### Structure du Projet

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Authentification mobile
│   │   │   ├── ProfileController.php       # Gestion profil mobile
│   │   │   ├── CompanyController.php       # CRUD entreprises
│   │   │   ├── PostController.php          # CRUD publications
│   │   │   └── Admin/
│   │   │       ├── AdminAuthController.php    # Auth admin
│   │   │       ├── AdminStatsController.php   # Statistiques
│   │   │       ├── AdminSearchController.php  # Recherches
│   │   │       └── AdminUserController.php    # Gestion admins
│   │   └── Middleware/
│   │       └── EnsureAdminAccess.php       # Middleware admin
│   ├── Models/
│   │   ├── User.php                        # Modèle utilisateur
│   │   ├── Company.php                     # Modèle entreprise
│   │   └── Post.php                        # Modèle publication
│   └── Providers/
│       └── AppServiceProvider.php          # Force HTTPS en prod
├── database/
│   ├── migrations/                         # Migrations
│   └── seeders/
│       └── DatabaseSeeder.php              # Créé admin par défaut
├── public/
│   └── admin/
│       └── index.html                      # Interface admin SPA
├── routes/
│   ├── api.php                             # Routes API
│   └── web.php                             # Routes web (admin)
├── storage/
│   └── api-docs/
│       └── api-docs.json                   # Documentation Swagger générée
└── config/
    └── l5-swagger.php                      # Config Swagger

```

### Tests

```bash
# Lancer tous les tests
php artisan test

# Lancer les tests avec couverture
php artisan test --coverage
```


- Les variables `DB_*` correspondent exactement à celles fournies par Render
- Le service web et la base de données sont dans la même région
- L'Internal Database URL est utilisée (pas l'External)

**Free tier se met en veille**

Le plan gratuit de Render met les services en veille après 15 minutes d'inactivité :
- Premier accès après veille : ~30-60 secondes de démarrage
- Solution : upgrader vers un plan payant ou utiliser un service de ping

#### 8. Monitoring et logs

**Accéder aux logs** :
1. Dashboard Render → votre service
2. Onglet "Logs"
3. Logs en temps réel du serveur PHP et des requêtes

**Health check** :
Render vérifie automatiquement `/up` toutes les minutes. Si le endpoint ne répond pas, le service est redémarré.

### Sécurité

#### Authentification

**Utilisateurs mobiles** :
- Tokens Sanctum générés via `/api/login`
- Tokens stockés de manière sécurisée via `flutter_secure_storage`
- Révocation automatique lors de la déconnexion

**Administrateurs** :
- Tokens Sanctum avec ability `admin`
- Middleware `EnsureAdminAccess` vérifie le rôle ET l'ability
- Tokens stockés dans localStorage (admin web) avec expiration automatique

#### Protection des routes

```php
// Middleware auth:sanctum pour les utilisateurs mobiles
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    // ...
});

// Double protection pour les routes admin
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/stats', AdminStatsController::class);
    // ...
});
```

#### CORS

Configuration CORS dans `config/cors.php` pour autoriser les requêtes depuis :
- L'application mobile Flutter
- Le domaine de production Render

#### HTTPS en production

Le `AppServiceProvider` force automatiquement HTTPS en production :

```php
if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```
 détails
- ✅ Gestion des erreurs avec snackbars contextuelles
- ✅ Navigation personnalisée avec bottom bar animée

### Technologies Utilisées

**Backend** :
- Laravel 12.38.1
- PHP 8.4.14
- Laravel Sanctum (authentification API)
- PostgreSQL (production) / MySQL (développement)
- L5-Swagger (documentation OpenAPI)

**Frontend Admin** :
- Tailwind CSS 3.x (via CDN)
- Font Awesome 6.x (icônes)
- Fetch API (requêtes HTTP)



### Auteurs

Développé par BALOGOU Urbain dans le cadre d'un test technique - Plateforme de mise en relation professionnelle.
