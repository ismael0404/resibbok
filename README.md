# ResiBook – Plateforme Intelligente de Réservation de Résidences

> Une plateforme web complète de type Airbnb construite en PHP/MySQL avec un design moderne et responsive.

---

## 🚀 Installation sous XAMPP

### Prérequis
- **XAMPP** installé avec Apache + MySQL activés
- **PHP 7.4+**
- **Navigateur moderne** (Chrome, Firefox, Edge)

### Étapes d'installation

1. **Copier le projet** dans le dossier `htdocs` de XAMPP :
   ```
   C:\xampp\htdocs\resibook\
   ```

2. **Activer le module `mod_rewrite`** dans Apache :
   - Ouvrir `C:\xampp\apache\conf\httpd.conf`
   - Décommenter la ligne : `LoadModule rewrite_module modules/mod_rewrite.so`
   - Redémarrer Apache

3. **Créer la base de données** :
   - Ouvrir **phpMyAdmin** : `http://localhost/phpmyadmin`
   - Créer une base nommée `resibook` (encodage `utf8mb4_unicode_ci`)
   - Importer le fichier `database/resibook.sql`

4. **Configurer la connexion** :
   - Ouvrir `config/config.php`
   - Vérifier les paramètres `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`
   - Vérifier `BASE_URL` (par défaut : `http://localhost/resibook`)

5. **Créer le dossier uploads** (si non existant) :
   ```
   mkdir uploads
   ```

6. **Accéder au site** : `http://localhost/resibook`

---

## 👤 Comptes de Test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| **Admin** | admin@resibook.com | password |
| **Propriétaire (validé)** | jean@resibook.com | password |
| **Propriétaire (validé)** | aminata@resibook.com | password |
| **Propriétaire (en attente)** | ibrahim@resibook.com | password |
| **Client** | fatou@resibook.com | password |
| **Client** | moussa@resibook.com | password |

> **Note** : Le mot de passe haché en base correspond à `password` (hash bcrypt standard).

---

## 📁 Architecture du Projet

```
resibook/
├── app/
│   ├── controllers/        # Contrôleurs MVC
│   │   ├── AdminController.php
│   │   ├── ApiController.php
│   │   ├── AuthController.php
│   │   ├── ClientController.php
│   │   ├── OwnerController.php
│   │   ├── PagesController.php
│   │   ├── ReservationsController.php
│   │   └── ResidencesController.php
│   ├── models/             # Modèles (accès base de données)
│   │   ├── Notification.php
│   │   ├── Reservation.php
│   │   ├── Residence.php
│   │   └── User.php
│   └── views/              # Vues (templates HTML/PHP)
│       ├── admin/
│       ├── auth/
│       ├── client/
│       ├── inc/             # Header & Footer partagés
│       ├── owner/
│       ├── pages/
│       └── residences/
├── config/
│   ├── config.php          # Configuration globale
│   └── Database.php        # Classe PDO
├── core/
│   ├── Controller.php      # Contrôleur de base
│   └── Router.php          # Routeur URL
├── database/
│   └── resibook.sql        # Script SQL complet
├── public/
│   ├── css/
│   │   └── style.css       # Feuille de styles principale
│   └── js/
│       └── app.js          # JavaScript principal
├── uploads/                # Images uploadées
├── .htaccess               # Réécriture d'URL Apache
└── index.php               # Point d'entrée
```

---

## ✨ Fonctionnalités

### Admin
- ✅ Dashboard avec statistiques (Chart.js)
- ✅ Validation obligatoire des propriétaires
- ✅ Approbation / rejet des propriétaires
- ✅ Gestion des utilisateurs et résidences

### Propriétaire
- ✅ Inscription avec statut `pending` (validation admin obligatoire)
- ✅ Dashboard revenus et réservations
- ✅ Ajout de résidences avec upload d'images multiples
- ✅ Gestion des équipements et règles
- ✅ Accepter / refuser les réservations

### Client
- ✅ Inscription / connexion
- ✅ Catalogue de résidences avec cartes modernes
- ✅ Page détail résidence avec galerie et widget de réservation
- ✅ Réservation avec calcul automatique du prix
- ✅ Vérification de disponibilité en temps réel (AJAX)
- ✅ Profil avec historique des voyages
- ✅ Wishlist (favoris)

### Bonus
- ✅ Mode sombre (Dark Mode)
- ✅ Notifications toast dynamiques
- ✅ API interne (`/api/residences`, `/api/reservations`, `/api/wishlist`)
- ✅ Design responsive (mobile-first)
- ✅ Animations CSS fluides
- ✅ SweetAlert2 pour les alertes

---

## 🔒 Sécurité
- `password_hash()` / `password_verify()` pour les mots de passe
- Requêtes préparées PDO (protection injection SQL)
- `htmlspecialchars()` pour la protection XSS
- Sessions sécurisées avec vérification de rôle
- Validation côté serveur de tous les formulaires
- Upload sécurisé (extension + taille vérifiées)

---

## 🛠️ Stack Technique
- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Backend** : PHP 7.4+ (architecture MVC)
- **Base de données** : MySQL / MariaDB
- **Librairies** : Chart.js, SweetAlert2, Font Awesome 6

---

## 📄 Licence
Projet académique – ResiBook © 2026
