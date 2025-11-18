# 🔐 Créer l'Administrateur en Production

## Problème
L'administrateur n'existe pas encore dans la base de données de production Render.

## Solution

### Méthode 1: Via le Shell Render (RECOMMANDÉE)

1. **Accéder au Shell Render**:
   - Aller sur https://dashboard.render.com
   - Cliquer sur votre service `proconnect-backend-2`
   - Cliquer sur l'onglet "Shell" en haut
   - Une console s'ouvrira dans votre navigateur

2. **Exécuter la commande suivante**:
   ```bash
   php artisan tinker --execute="
   \$admin = \App\Models\User::create([
       'name' => 'Admin ProConnect',
       'email' => 'admin@proconnect.test',
       'password' => 'password123',
       'address' => '123 Admin Street, Production',
       'account_type' => 'pro',
       'role' => 'admin'
   ]);
   echo 'Admin créé avec succès: ' . \$admin->email . ' (ID: ' . \$admin->id . ')';
   "
   ```

3. **Vérifier la création**:
   ```bash
   php artisan tinker --execute="
   \$admin = \App\Models\User::where('email', 'admin@proconnect.test')->first();
   echo 'Admin trouvé: ' . \$admin->name . ' (Role: ' . \$admin->role . ')';
   "
   ```

4. **Se connecter**:
   - URL: https://proconnect-backend-2-6sn4.onrender.com/admin
   - Email: `admin@proconnect.test`
   - Password: `password123`

---

### Méthode 2: Via le Seeder (Si migrations non exécutées)

Si la base de données est vide, exécutez simplement:

```bash
php artisan migrate --seed
```

Cela créera automatiquement l'admin avec les identifiants par défaut.

---

### Méthode 3: Script SQL Direct

Si vous avez accès à la base de données PostgreSQL directement:

```sql
INSERT INTO users (name, email, password, address, account_type, role, created_at, updated_at)
VALUES (
    'Admin ProConnect',
    'admin@proconnect.test',
    '$2y$12$AIFZorcCQYVSmFBcCYsrxe2qwixII/t1Fn1zR0hnyZalE3k.K70q.',  -- password123
    '123 Admin Street, Production',
    'pro',
    'admin',
    NOW(),
    NOW()
);
```

---

## ⚠️ IMPORTANT - Sécurité en Production

**APRÈS LA PREMIÈRE CONNEXION**, changez IMMÉDIATEMENT le mot de passe:

1. Via Tinker (Shell Render):
   ```bash
   php artisan tinker --execute="
   \$admin = \App\Models\User::where('email', 'admin@proconnect.test')->first();
   \$admin->password = 'VotreNouveauMotDePasseSécurisé123!@#';
   \$admin->save();
   echo 'Mot de passe admin modifié avec succès';
   "
   ```

2. Ou créez un nouveau compte admin avec l'interface et supprimez celui par défaut.

---

## Vérification

Testez la connexion via l'API:

```bash
curl -X POST "https://proconnect-backend-2-6sn4.onrender.com/api/admin/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@proconnect.test","password":"password123"}'
```

Vous devriez recevoir:
```json
{
  "token": "...",
  "admin": {
    "id": 1,
    "name": "Admin ProConnect",
    "email": "admin@proconnect.test",
    "role": "admin",
    ...
  }
}
```

---

## Résolution de Problèmes

### "Duplicate entry" error
L'admin existe déjà. Réinitialisez le mot de passe:
```bash
php artisan tinker --execute="
\$admin = \App\Models\User::where('email', 'admin@proconnect.test')->first();
\$admin->password = 'password123';
\$admin->save();
"
```

### "Connection refused"
Le service Render est peut-être en veille. Attendez 30 secondes et réessayez.

### "Class User not found"
Autoload cache corrompu:
```bash
composer dump-autoload
php artisan config:clear
```

---

## Après Création

Une fois l'admin créé, vous pourrez:

✅ Vous connecter à: https://proconnect-backend-2-6sn4.onrender.com/admin
✅ Accéder au dashboard avec statistiques
✅ Gérer utilisateurs, entreprises, publications
✅ Créer d'autres administrateurs
✅ Consulter la documentation Swagger: /docs

---

## Contact Support

Si vous rencontrez des problèmes:
1. Vérifier les logs Render: Dashboard → Logs
2. Vérifier la connexion DB dans les variables d'environnement
3. Contacter le support Render si problème de service
