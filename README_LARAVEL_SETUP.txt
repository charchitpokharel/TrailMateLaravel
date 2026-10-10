TRAILMATE — LARAVEL CONVERSION
==============================

This package converts the supplied CodeIgniter 4 TrailMate app features into Laravel MVC files:
- Trail listing, create, view, edit, delete
- Live trail search JSON endpoint
- Session-based favourites
- Trail reviews
- Current weather via Open-Meteo
- Bootstrap responsive interface and CSRF-aware forms

IMPORTANT: This is an application overlay, not a complete Laravel framework distribution.
Create a fresh Laravel project first, then copy/merge these files into it. Do not replace Laravel's whole app directory.

1. Install PHP/Composer and create a Laravel project:
   composer create-project laravel/laravel TrailMate
   cd TrailMate

2. Copy the folders from this package into the matching folders in the new Laravel project:
   app/
   database/
   resources/
   routes/

   If asked, merge folders; keep Laravel's existing core files.

3. Configure the database in the Laravel project's .env file. Example MySQL:
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=trailmate
   DB_USERNAME=root
   DB_PASSWORD=

   Create the database named trailmate in MySQL/phpMyAdmin first. Change credentials to match your setup.

4. Run:
   php artisan key:generate
   php artisan migrate
   php artisan serve

5. Open http://127.0.0.1:8000

NOTES
- Laravel's default web middleware provides sessions and CSRF verification for routes in routes/web.php.
- Favourites are stored in the session, not linked to user accounts.
- No authentication/authorization is included, matching the supplied CodeIgniter app. Add access control before public deployment because trail create/edit/delete routes are currently open.
- Weather requires an internet connection to reach Open-Meteo and browser location permission. Geolocation generally requires localhost or HTTPS.
- This package has not been run against your local PHP/MySQL installation. Check PHP/Laravel compatibility and your database credentials if errors appear.
