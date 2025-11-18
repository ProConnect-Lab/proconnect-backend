# 🔐 Identifiants de Connexion - ProConnect

## Interface d'Administration Web

**URL** : http://localhost:8000/admin (développement)
**URL** : https://votre-app.onrender.com/admin (production)

### Compte Administrateur par Défaut

```
Email    : admin@proconnect.test
Password : password123
```

⚠️ **IMPORTANT** : Changez ce mot de passe immédiatement en production !

---

## Documentation API (Swagger)

**URL** : http://localhost:8000/docs (développement)
**URL** : https://votre-app.onrender.com/docs (production)

### Comment tester l'API avec Swagger

1. **Se connecter en tant qu'admin** :
   - Aller dans la section "Admin - Authentication"
   - Utiliser l'endpoint `POST /api/admin/login`
   - Entrer les identifiants :
     ```json
     {
       "email": "admin@proconnect.test",
       "password": "password123"
     }
     ```
   - Récupérer le `token` dans la réponse

2. **Autoriser les requêtes** :
   - Cliquer sur le bouton "Authorize" 🔓 en haut à droite
   - Entrer : `Bearer votre_token_ici`
   - Cliquer sur "Authorize" puis "Close"

3. **Tester les endpoints** :
   - Tous les endpoints sont maintenant accessibles
   - Cliquer sur "Try it out" pour tester chaque endpoint

---

## Endpoints Disponibles

### 📱 Mobile (Utilisateurs)

**Authentication**
- `POST /api/register` - Inscription
- `POST /api/login` - Connexion
- `POST /api/logout` - Déconnexion

**Profile**
- `GET /api/profile` - Récupérer le profil
- `PUT /api/profile` - Modifier le profil

**Companies**
- `GET /api/companies` - Liste des entreprises
- `POST /api/companies` - Créer une entreprise
- `PUT /api/companies/{id}` - Modifier une entreprise
- `DELETE /api/companies/{id}` - Supprimer une entreprise

**Posts**
- `GET /api/posts` - Liste des publications (avec recherche)
- `POST /api/posts` - Créer une publication
- `PUT /api/posts/{id}` - Modifier une publication
- `DELETE /api/posts/{id}` - Supprimer une publication

### 👨‍💼 Admin (Interface Web)

**Admin Authentication**
- `POST /api/admin/login` - Connexion admin
- `POST /api/admin/logout` - Déconnexion admin
- `GET /api/admin/me` - Profil admin

**Admin Stats**
- `GET /api/admin/stats` - Statistiques globales

**Admin Search & CRUD**
- `GET /api/admin/users` - Recherche utilisateurs
- `DELETE /api/admin/users/{id}` - Supprimer utilisateur

- `GET /api/admin/companies` - Recherche entreprises
- `GET /api/admin/companies/all` - Liste toutes les entreprises
- `DELETE /api/admin/companies/{id}` - Supprimer entreprise

- `GET /api/admin/posts` - Recherche publications
- `POST /api/admin/posts` - Créer publication (admin)
- `PUT /api/admin/posts/{id}` - Modifier publication (propre)
- `DELETE /api/admin/posts/{id}` - Supprimer publication

**Admin Users Management**
- `GET /api/admin/admins` - Liste des administrateurs
- `POST /api/admin/admins` - Créer un administrateur

---

## Créer des Utilisateurs de Test

### Via Swagger UI

1. **Créer un utilisateur mobile** :
   - Endpoint : `POST /api/register`
   - Body :
     ```json
     {
       "name": "Test User",
       "email": "test@example.com",
       "password": "password123",
       "password_confirmation": "password123",
       "address": "123 Test Street",
       "account_type": "pro"
     }
     ```

2. **Créer un admin** (requiert connexion admin) :
   - Endpoint : `POST /api/admin/admins`
   - Body :
     ```json
     {
       "name": "Admin Nouveau",
       "email": "admin2@proconnect.test",
       "password": "password123",
       "password_confirmation": "password123",
       "address": "456 Admin Street",
       "account_type": "pro"
     }
     ```

### Via Tinker (ligne de commande)

```bash
php artisan tinker

# Créer un utilisateur
\App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'address' => '123 Test Street',
    'account_type' => 'pro',
    'role' => 'user'
]);

# Créer un admin
\App\Models\User::create([
    'name' => 'Admin Nouveau',
    'email' => 'admin2@proconnect.test',
    'password' => 'password123',
    'address' => '456 Admin Street',
    'account_type' => 'pro',
    'role' => 'admin'
]);
```

---

## Réinitialiser le Mot de Passe Admin

```bash
php artisan tinker

$admin = \App\Models\User::where('email', 'admin@proconnect.test')->first();
$admin->password = 'nouveau_mot_de_passe';
$admin->save();
```

---

## État de la Documentation

✅ **20 endpoints documentés dans Swagger**

**Tags disponibles** :
- Authentication (mobile)
- Profile (mobile)
- Companies (mobile)
- Posts (mobile)
- Admin - Authentication
- Admin - Stats
- Admin - Search (CRUD complet)
- Admin - Users

**Fichier généré** : `storage/api-docs/api-docs.json`

**Régénérer la documentation** :
```bash
php artisan l5-swagger:generate
```

---

## Support

Pour toute question ou problème :
1. Vérifier les logs : `tail -f storage/logs/laravel.log`
2. Vider le cache : `php artisan config:clear && php artisan cache:clear`
3. Consulter le README.md pour plus de détails
