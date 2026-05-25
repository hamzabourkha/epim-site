# EPIM - Site officiel Laravel

Site web professionnel pour **EPIM - Ecole Professionnelle d Informatique et de Management**, etablissement prive de formation professionnelle a Meknes.

## Stack

- Laravel 11 / PHP 8.2+
- MySQL
- Blade, Bootstrap 5, JavaScript moderne
- Dashboard admin securise
- Seeders avec donnees de demonstration realistes

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
php artisan serve
```

Configurer `.env` avec vos acces MySQL:

```env
DB_DATABASE=epim_site
DB_USERNAME=root
DB_PASSWORD=
```

## Acces administration

- URL: `/login`
- Email: `admin@epim.ma`
- Mot de passe: `password`

Roles crees: `admin`, `directeur`, `assistant`, `formateur`.

## Pages publiques

- Accueil
- A propos
- Filieres / formations avec fiches detaillees
- Admission / inscription avec upload documents et numero dossier
- Actualites / blog avec recherche, categories et pagination
- Galerie filtrable
- Entreprises et partenariats
- Contact avec formulaire AJAX

## Modules admin

- Dashboard avec statistiques, activite recente et notifications
- Gestion actualites
- Gestion formations
- Gestion galerie
- Gestion inscriptions
- Gestion messages contact
- Gestion partenaires
- Gestion utilisateurs

## Production cPanel

Pointer le domaine vers `public/`, renseigner les variables `.env`, executer les migrations/seeders, puis:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Le fichier `public/robots.txt`, `public/sitemap.xml`, les balises SEO, OpenGraph et schema.org sont inclus.
