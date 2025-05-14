# API d'Authentification JWT en PHP

Une API d'authentification simple utilisant les tokens JWT (JSON Web Tokens), développée en PHP pur avec une architecture MVC.

## Prérequis

- PHP 8.3 ou supérieur
- MySQL 8.0 ou supérieur
- Composer
- Serveur web (Apache ou Nginx)

## Installation

1. Clonez ce dépôt :
   ```bash
   git clone https://github.com/votre-nom/jwt-auth-api.git
   cd jwt-auth-api
   ```

2. Installez les dépendances via Composer :
   ```bash
   composer install
   ```

3. Configurez votre base de données :
   - Créez une base de données MySQL
   - Modifiez le fichier `config/config.php` avec vos informations de connexion
   - Exécutez le script SQL contenu dans `database.sql` pour créer les tables nécessaires

4. Configurez votre serveur web pour pointer vers le dossier du projet, avec le support de la réécriture d'URL.

## Documentation de l'API

### Endpoints

#### 1. Inscription

- **URL** : `/api/auth/register`
- **Méthode** : `POST`
- **Corps de la requête** :
  ```json
  {
    "email": "utilisateur@exemple.com",
    "password": "motdepasse",
    "firstname": "John",
    "lastname": "Doe"
  }
  ```
- **Réponse de succès** :
  ```json
  {
    "status": "success",
    "message": "Inscription réussie",
    "data": {
      "id": 1
    }
  }
  ```

#### 2. Connexion

- **URL** : `/api/auth/login`
- **Méthode** : `POST`
- **Corps de la requête** :
  ```json
  {
    "email": "utilisateur@exemple.com",
    "password": "motdepasse"
  }
  ```
- **Réponse de succès** :
  ```json
  {
    "status": "success",
    "message": "Connexion réussie",
    "data": {
      "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
      "expires_in": 300
    }
  }
  ```

#### 3. Consultation du profil

- **URL** : `/api/user/profile`
- **Méthode** : `GET`
- **En-têtes** :
  ```
  X-AUTH-TOKEN: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
  ```
- **Réponse de succès** :
  ```json
  {
    "status": "success",
    "message": "Profil récupéré avec succès",
    "data": {
      "id": 1,
      "email": "utilisateur@exemple.com",
      "firstname": "John",
      "lastname": "Doe",
      "created_at": "2023-05-13 10:00:00",
      "updated_at": "2023-05-13 10:00:00"
    }
  }
  ```

#### 4. Mise à jour du profil

- **URL** : `/api/user/profile`
- **Méthode** : `PUT`
- **En-têtes** :
  ```
  X-AUTH-TOKEN: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
  ```
- **Corps de la requête** :
  ```json
  {
    "firstname": "John Updated",
    "lastname": "Doe Updated"
  }
  ```
- **Réponse de succès** :
  ```json
  {
    "status": "success",
    "message": "Profil mis à jour avec succès",
    "data": {
      "id": 1,
      "email": "utilisateur@exemple.com",
      "firstname": "John Updated",
      "lastname": "Doe Updated",
      "created_at": "2023-05-13 10:00:00",
      "updated_at": "2023-05-13 11:00:00"
    }
  }
  ```

### Codes de statut HTTP

- `200 OK` : Requête traitée avec succès
- `201 Created` : Ressource créée avec succès
- `400 Bad Request` : La requête n'a pas pu être traitée à cause d'un problème client
- `401 Unauthorized` : Authentification requise ou échec de l'authentification
- `404 Not Found` : Ressource non trouvée
- `409 Conflict` : Conflit avec l'état actuel de la ressource
- `500 Internal Server Error` : Erreur interne du serveur

## Architecture du projet

Ce projet suit une architecture MVC (Modèle-Vue-Contrôleur) :

- **Modèles** : Contiennent la logique métier et interagissent avec la base de données
- **Contrôleurs** : Gèrent les requêtes HTTP et appellent les modèles appropriés
- **Vues** : Dans cette API REST, les "vues" sont les réponses JSON

## Sécurité

- Les mots de passe sont hachés avec la fonction `password_hash()` de PHP
- Les tokens JWT sont valables pendant 5 minutes
- Toutes les entrées utilisateur sont validées et filtrées