# 🎰 Casino Access System - PHP/MySQL

Système complet de gestion d'accès par code pour jeux de casino en ligne. Interface admin pour générer des codes avec montant, interface joueur pour accéder aux jeux.

## 🌟 Fonctionnalités

### 🎮 Pour les Joueurs
- **Page d'accueil élégante** avec saisie de code
- **Tableau de bord** avec solde et historique
- **Accès aux jeux** KENO et SLOTS
- **Interface responsive** mobile/desktop
- **Historique des transactions**

### 👨‍💼 Pour l'Administrateur
- **Génération de codes** avec montant personnalisé
- **Gestion des expirations** (1, 7, 30, 90 jours ou illimité)
- **Tableau de bord** avec statistiques
- **Visualisation** des codes et transactions
- **Interface admin sécurisée**

### 🗄️ Base de Données
- **MySQL** avec tables optimisées
- **Codes uniques** avec montant et expiration
- **Historique des transactions**
- **Statistiques en temps réel**
- **Sécurité** avec préparation des requêtes

## 🚀 Installation

### Prérequis
- **XAMPP/WAMP/LAMP** (Apache, PHP, MySQL)
- **PHP 7.4+** avec PDO MySQL
- **MySQL 5.7+**
- **Navigateur web moderne**

### Étapes d'installation

1. **Téléchargez le projet**
```bash
git clone https://github.com/votre-repo/casino-access-system.git
cd casino-access-system
```

2. **Configurez la base de données**
```sql
-- Exécutez database.sql dans phpMyAdmin
-- Ou en ligne de commande:
mysql -u root -p < database.sql
```

3. **Configurez les paramètres**
Éditez `config.php` avec vos informations de base de données:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'casino_access_system');
```

4. **Copiez les jeux**
```bash
# Téléchargez les jeux depuis GitHub
cd games
git clone https://github.com/afrique2info-cyber/keno-html5.git keno
git clone https://github.com/afrique2info-cyber/html5-slots-game.git slots
```

5. **Accédez au site**
```
http://localhost/casino-access-system/
```

## 📁 Structure du Projet

```
casino-access-system/
├── index.php              # Page d'accueil (saisie code)
├── dashboard.php          # Tableau de bord joueur
├── play.php              # Intégration des jeux
├── config.php            # Configuration
├── logout.php            # Déconnexion
├── database.sql          # Structure base de données
├── admin/                # Interface administrateur
│   ├── index.php        # Dashboard admin
│   ├── login.php        # Connexion admin
│   └── logout.php       # Déconnexion admin
├── api/                  # API REST
│   └── get_balance.php  # Récupération solde
├── games/               # Jeux HTML5
│   ├── keno/           # Jeu KENO
│   └── slots/          # Jeu SLOTS
├── css/                 # Styles CSS
├── js/                  # JavaScript
└── includes/            # Fichiers inclus
```

## 🔐 Sécurité

- **Préparation des requêtes PDO** (protection injection SQL)
- **Sessions PHP sécurisées**
- **Validation des entrées utilisateur**
- **Codes aléatoires uniques**
- **Expiration des sessions**
- **Protection contre les attaques CSRF**

## 🎨 Personnalisation

### Changer le thème
Éditez les styles dans chaque fichier PHP ou créez `css/style.css`.

### Modifier les paramètres
Éditez `config.php`:
- Longueur des codes
- Préfixe des codes
- URLs des jeux
- Timeout des sessions

### Ajouter des jeux
1. Ajoutez le jeu dans `games/`
2. Mettez à jour `config.php`
3. Ajoutez l'option dans `dashboard.php`

## 📊 Base de Données

### Tables principales
- **access_codes** : Codes d'accès avec montant
- **transactions** : Historique des parties
- **admin_users** : Administrateurs

### Vues
- **code_statistics** : Statistiques des codes

## 👥 Utilisation

### Pour l'administrateur
1. Connectez-vous à `http://localhost/casino-access-system/admin/`
2. Identifiants: `admin` / `admin123`
3. Générez des codes avec montant
4. Suivez les statistiques

### Pour les joueurs
1. Accédez à `http://localhost/casino-access-system/`
2. Entrez le code fourni par l'admin
3. Jouez aux jeux avec le solde attribué

## 🛠️ Développement

### Environnement recommandé
- **Visual Studio Code** avec extensions PHP
- **XAMPP** pour serveur local
- **phpMyAdmin** pour gestion base de données
- **Git** pour versioning

### Tests
- Testez la génération de codes
- Testez l'accès avec différents codes
- Vérifiez l'historique des transactions
- Testez sur mobile et desktop

## 📄 License

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

## 🙏 Support

Pour toute question ou problème:
1. Vérifiez la configuration de la base de données
2. Vérifiez les permissions des dossiers
3. Consultez les logs Apache/PHP
4. Ouvrez une issue sur GitHub

---

**Développé
 avec ❤️ pour les plateformes de jeux en ligne**

## 🚀 Déploiement en Production

### Configuration serveur
1. **Hébergement** avec PHP et MySQL
2. **Base de données** sécurisée
3. **SSL/TLS** pour les connexions sécurisées
4. **Backup** régulier de la base de données

### Optimisations
1. Activer le cache PHP
2. Optimiser les requêtes MySQL
3. Minifier les CSS/JS
4. Configurer CDN pour les jeux

### Sécurité production
1. Changer les mots de passe par défaut
2. Configurer .htaccess pour protection
3. Limiter les tentatives de connexion
4. Journaliser les activités suspectes
