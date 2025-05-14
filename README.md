# API d'Authentification JWT

Ce projet est une API d'authentification basée sur JSON Web Tokens (JWT), développée en PHP 8.3 avec MySQL 8.0, suivant une architecture MVC sans utilisation de frameworks fullstack.

## Table des matières

- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Lancement](#lancement)
- [Documentation de l'API](#documentation-de-lapi)
  - [Inscription](#1-inscription)
  - [Connexion](#2-connexion)
  - [Consultation des informations utilisateur](#3-consultation-des-informations-utilisateur)
  - [Mise à jour des informations personnelles](#4-mise-à-jour-des-informations-personnelles)
- [Structure du projet](#structure-du-projet)
- [Sécurité](#sécurité)

## Prérequis

- PHP 8.3 ou supérieur
- MySQL 8.0
- Composer

## Installation

1. Clonez ce dépôt :
```bash
git clone git@github.com:b1gdanhe/test_technique_dev_backend.git
cd test_technique_dev_backend
```

2. Installez les dépendances via Composer :
```bash
composer install
```

3. Créez une base de données MySQL pour le projet.

## Configuration

1. Configurez la connexion à la base de données dans `config/database.php` :
```php
<?php
return [
    'db_system' => 'mysql',
    'db_name' => 'test_technique',
    'db_host' => 'localhost',
    'db_username' => 'root',
    'db_password' => 'Big@big1'
];
```

2. Configurez les paramètres JWT dans `config/jwt.php` :
```php
<?php
return [
    'secret_key' => 'votre_clé_secrète', // À remplacer par une clé forte
    'expiration_time' => 300 // Durée de validité du token en secondes (5 minutes)
];
```

3. Créez les tables nécessaires en exécutant le script :
```bash
php core/create_tables.php
```

## Lancement

Pour lancer l'application en local :

```bash
cd public
php -S localhost:8000 
ou directement dans à la racine du projet
php -S localhost:8000 -t public 
```

L'API sera accessible à l'adresse : http://localhost:8000

## Documentation de l'API

Toutes les requêtes et réponses sont au format JSON.

### 1. Inscription

Permet à un utilisateur de s'inscrire en fournissant une adresse email et un mot de passe.

- **URL** : `/api/register`
- **Méthode** : `POST`
- **En-têtes** : `Content-Type: application/json`
- **Corps de la requête** :
```json
{
    "email": "utilisateur@exemple.com",
    "password": "mot_de_passe"
}
```

#### Réponses

**Succès (201 Created)**
```json
{
    "status": "success",
    "message": "Utilisateur créé avec succès"
}
```

**Erreur - Email déjà utilisé (409 Conflict)**
```json
{
    "status": "error",
    "message": "Cet email est déjà utilisé"
}
```

**Erreur - Données invalides (400 Bad Request)**
```json
{
    "status": "error",
    "message": "Email et mot de passe requis"
}
```

### 2. Connexion

Permet à un utilisateur de se connecter et d'obtenir un token JWT valide pour 5 minutes.

- **URL** : `/api/login`
- **Méthode** : `POST`
- **En-têtes** : `Content-Type: application/json`
- **Corps de la requête** :
```json
{
    "email": "utilisateur@exemple.com",
    "password": "mot_de_passe"
}
```

#### Réponses

**Succès (200 OK)**
```json
{
    "status": "success",
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

**Erreur - Identifiants invalides (401 Unauthorized)**
```json
{
    "status": "error",
    "message": "Email ou mot de passe incorrect"
}
```

### 3. Consultation des informations utilisateur

Permet à l'utilisateur authentifié de consulter ses informations personnelles.

- **URL** : `/api/user`
- **Méthode** : `GET`
- **En-têtes** : 
  - `Content-Type: application/json`
  - `X-AUTH-TOKEN: votre_token_jwt`

#### Réponses

**Succès (200 OK)**
```json
{
    "status": "success",
    "user": {
        "email": "utilisateur@exemple.com",
        "firstname": "Prénom",
        "lastname": "Nom"
    }
}
```

**Erreur - Non authentifié (401 Unauthorized)**
```json
{
    "status": "error",
    "message": "Non authentifié"
}
```

**Erreur - Token invalide ou expiré (401 Unauthorized)**
```json
{
    "status": "error",
    "message": "Token invalide ou expiré"
}
```

### 4. Mise à jour des informations personnelles

Permet à l'utilisateur authentifié de mettre à jour son nom et prénom.

- **URL** : `/api/user/profile`
- **Méthode** : `PUT`
- **En-têtes** : 
  - `Content-Type: application/json`
  - `X-AUTH-TOKEN: votre_token_jwt`
- **Corps de la requête** :
```json
{
    "firstname": "Nouveau Prénom",
    "lastname": "Nouveau Nom"
}
```

#### Réponses

**Succès (200 OK)**
```json
{
    "status": "success",
    "message": "Profil mis à jour avec succès",
    "user": {
        "email": "utilisateur@exemple.com",
        "firstname": "Nouveau Prénom",
        "lastname": "Nouveau Nom"
    }
}
```

**Erreur - Non authentifié (401 Unauthorized)**
```json
{
    "status": "error",
    "message": "Non authentifié"
}
```

**Erreur - Token invalide ou expiré (401 Unauthorized)**
```json
{
    "status": "error",
    "message": "Token invalide ou expiré"
}
```

## Structure du projet

```
├── README.md
├── composer.json
├── composer.lock
├── config
│   ├── database.php
│   └── jwt.php
├── core
│   ├── App.php
│   ├── Database.php
│   ├── JWT.php
│   ├── Request.php
│   ├── Response.php
│   ├── create_tables.php
│   └── helpers.php
├── public
│   └── index.php
├── request-test.http
├── src
│   ├── controller
│   │   ├── AuthController.php
│   │   └── UserController.php
│   ├── middleware
│   │   └── AuthMiddleware.php
│   ├── model
│   │   └── User.php
```

## Sécurité

- Les mots de passe sont hachés avant d'être stockés en base de données
- L'authentification est gérée via JWT avec un délai d'expiration de 5 minutes
- Toutes les requêtes d'API nécessitant une authentification sont protégées par un middleware de vérification de token
- Les validations d'entrée sont effectuées pour prévenir les injections SQL et autres vulnérabilités