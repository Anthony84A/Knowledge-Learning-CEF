# Knowledge Learning

Knowledge Learning est une plateforme e-learning et e-commerce développée avec **Symfony 7.3** et **PHP 8.2**, permettant aux utilisateurs d’acheter des cursus ou des leçons et d’obtenir des certifications après validation.

---

## Technologies utilisées

- **PHP** >= 8.2  
- **Symfony** 7.3  
- **Doctrine ORM** 3.5  
- **MySQL / MariaDB**  
- **Twig** pour le templating  
- **Dompdf** pour la génération de PDF  
- **Stripe** pour la simulation d’achats  
- **PhpUnit** 11 pour les tests unitaires  

---

## Prérequis

- PHP >= 8.2 avec les extensions : ctype, iconv  
- Composer  
- MySQL ou MariaDB  
- Serveur web (ex : Apache via XAMPP)  
- Node.js et npm (si utilisation d’assets frontend, optionnel)  

---

## Installation

1. **Cloner le dépôt :**
```bash
git clone <URL_DU_REPO>
cd knowledge_learning
Installer les dépendances PHP :

bash
Copier le code
composer install
Configurer l’environnement :

Copier le fichier .env en .env.local pour vos paramètres locaux.

Modifier la variable DATABASE_URL pour votre base de données locale si nécessaire.

Ajouter vos clés Stripe dans .env.local (vous devez créer un compte Stripe pour obtenir ces clés) :

dotenv
Copier le code
STRIPE_PUBLIC_KEY="votre_cle_publique"
STRIPE_SECRET_KEY="votre_cle_secrete"
MAILER_DSN="smtp://email:password@smtp.gmail.com:587"
Créer et migrer la base de données :

bash
Copier le code
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
Charger les fixtures initiales :

bash
Copier le code
php bin/console doctrine:fixtures:load
Après le chargement des fixtures, le projet contient :

1 compte administrateur :
Email : admin@site.com
Mot de passe : admin123

2 comptes utilisateurs :
Email : user1@site.com, Mot de passe : password
Email : user2@site.com, Mot de passe : password

Lancer le serveur Symfony :

bash
Copier le code
symfony server:start
Accédez à l’application via http://127.0.0.1:8000

Tests unitaires
Pour exécuter les tests unitaires sur la base de données de test :

bash
Copier le code
php bin/phpunit --env=test
Fonctionnalités principales
Inscription et activation par email

Authentification sécurisée avec rôle utilisateur et administrateur

Achat simulé de cursus et leçons via Stripe sandbox

Validation de leçons et obtention de certifications

Backoffice pour la gestion des utilisateurs, contenus et achats

Documentation
Le code est commenté en anglais et documenté via phpDocumentor pour faciliter la maintenance et la lecture.

Architecture du projet
Thème → contient un ou plusieurs Cursus

Cursus → contient plusieurs Leçons

Chaque Leçon → fiche + vidéo

Users → rôles : ROLE_ADMIN et ROLE_USER

Gestion des achats via Stripe sandbox

Validation automatique des leçons et cursus

Composants séparés pour l’accès aux données (Doctrine ORM)

Base de données
Tables principales : user, theme, cursus, lesson, lesson_validation, certification, purchase

Chaque table possède les champs : created_at, updated_at, created_by, updated_by

Mots de passe cryptés (Bcrypt)

Rôle utilisateur pour sécuriser l’accès aux fonctionnalités