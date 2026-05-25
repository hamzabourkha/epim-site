# Déploiement du site EPIM sur serveur

## Prérequis

- PHP 8.2 ou supérieur
- MySQL
- Extensions PHP Laravel habituelles : `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
- Domaine pointé vers le dossier `public`

## Installation recommandée

1. Uploader l’archive `epim-site-production.zip` sur le serveur.
2. Décompresser l’archive dans un dossier du serveur, par exemple `epim-site`.
3. Pointer le domaine vers `epim-site/public`.
4. Copier `.env.production.example` vers `.env`.
5. Modifier `.env` avec les accès MySQL, l’URL du domaine et les paramètres email.
6. Exécuter :

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Alternative sans SSH

Si l’hébergeur ne permet pas d’exécuter les commandes Artisan :

1. Créer une base MySQL depuis cPanel.
2. Importer le fichier `database/epim_site_mysql.sql` avec phpMyAdmin.
3. Copier `.env.production.example` vers `.env`.
4. Modifier `.env` avec les accès MySQL et l’URL du domaine.
5. Vérifier que le domaine pointe vers le dossier `public`.

Dans ce cas, il faudra quand même générer une `APP_KEY`. Si vous n’avez pas SSH, je peux vous préparer une clé à coller dans `.env`.

## Si le serveur est cPanel

### Option propre

Mettre le projet Laravel hors `public_html`, puis configurer le domaine ou sous-domaine pour pointer vers le dossier `public`.

### Option si le domaine pointe seulement vers `public_html`

Uploader le contenu du dossier `public` dans `public_html`, puis adapter `public_html/index.php` pour pointer vers les dossiers Laravel situés hors `public_html`.

## Connexion administration

- URL : `/login`
- Email : `admin@epim.ma`
- Mot de passe : `password`

Après la mise en production, changer ce mot de passe depuis le back-office.

## Permissions importantes

Les dossiers suivants doivent être inscriptibles par PHP :

- `storage`
- `bootstrap/cache`

## Après chaque modification importante

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
