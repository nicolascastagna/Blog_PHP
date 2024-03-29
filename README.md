# Projet 5 - Créez votre premier blog en PHP

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/43b9019771ce48a7856b43fbb2c3cb22)](https://app.codacy.com/gh/nicolascastagna/Blog_PHP?utm_source=github.com&utm_medium=referral&utm_content=nicolascastagna/Blog_PHP&utm_campaign=Badge_Grade)

### Parcours Développeur d'application PHP/Symfony

## Prérequis

- Serveur local sous PHP 8.2 ([MAMP](https://www.wampserver.com/) pour macOs ou [WAMP](https://www.mamp.info/en/mamp/mac/) pour windows)
- Base de donnée MySQL
- [Composer](https://getcomposer.org/)
  
## Installation du projet

**1 - Cloner le dépôt GitHub :**
```
git clone https://github.com/nicolascastagna/Blog_PHP.git
```

**2 - Installer les dépendances :**
```
composer install
```

**3 - Copier le fichier **.env.example** et renommer le en **.env** et modifier les paramètres de connexion à la base de données / gmail :**
```
DB_HOST=localhost
DB_NAME=BlogPHP
DB_USER=your_database_user
DB_PASSWORD=your_database_password

MAIL_HOST=your_smtp_host
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=ssl
MAIL_PORT=465
MAIL_FROM_ADDRESS=from_address@example.com
```

**4 - Créer la base de données :**   

Importer le fichier sql **database.sql**, présent à la racine du projet, dans votre base de donnée.

**5 - Démarrer le serveur web :**   

Ajoutez ou modifier votre fichier **httpd-vhosts.conf** afin de pointer vers le dossier **public** du projet ou alternativement cette commande :
```
php -S localhost:8080
```

**6 - Informations de connexions utilisateurs par défaut**

**Rôle Admin**
- **Email de connexion :** admin@nicolascastagna.com
- **Mot de passe :** 5f66a2f90e8b79adfc10d94e3923d7ab

**Rôle Utilisateur**
- **Email de connexion :** user@nicolascastagna.com
- **Mot de passe :** 5f66a2f90e8b79adfc10d94e3923d7ab

**7 - Paramétrage de Gmail**

Il est nécessaire de configurer votre gmail pour pouvoir tester l'envois de mail. Accédez aux paramètres de sécurité de votre compte Google, recherchez l'option **Mots de passe d'application** et générez un [mot de passe d'application](https://myaccount.google.com/apppasswords) pour PHPMailer.