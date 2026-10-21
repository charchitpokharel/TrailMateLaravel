TRAILMATE LARAVEL — PROFESSIONAL MVC + AJAX + ADMIN OVERLAY
================================================

WHAT THIS PACKAGE IS
--------------------
This ZIP contains the TrailMate application files to merge into the Laravel 12 project you already created. It is not a fresh Laravel framework installation and does not include vendor/, .env, or generated framework dependencies. Keep your existing Laravel skeleton and merge these files into it.

FEATURES INCLUDED
-----------------
- Laravel MVC structure: controllers, Eloquent models, Blade views, migrations, routes.
- Responsive Bootstrap layout for desktop, tablet and mobile.
- Trail CRUD: list, create, view, edit and delete.
- AJAX trail search, difficulty filtering, pagination and result counts (no full
  page reload while searching/filtering/paging).
- AJAX trail create/update with inline server-side validation errors.
- AJAX delete and session-based favourite toggling.
- AJAX reviews: submit and refresh the review list without reloading the page.
- Weather lookup through Open-Meteo using browser geolocation.
- CSRF-aware same-origin requests and escaped output for AJAX-rendered text.
- Existing API-style endpoints/named routes are retained.

IMPORTANT LIMITATIONS
---------------------
- Authentication uses Laravel Breeze routes already installed in your project. Admin routes are protected by AdminMiddleware; ordinary users may manage only trails they own. Verify these controls locally before deployment.
- Favourites are browser-session based, not persisted to user accounts.
- Weather requires internet access and geolocation permission; browsers generally
  require localhost or HTTPS for location.
- This overlay has not been executed against your local database/PHP setup. Do
  the verification steps below after merging.
- AJAX is used for interactive operations and trail result updates. Ordinary
  navigation between separate pages still uses normal Laravel links.

INSTALL / MERGE
---------------
1. Back up your existing project folder first.
2. Extract this ZIP.
3. Copy/merge the contents of TrailMate_Laravel/ into the matching folders of
   your existing Laravel project (for example C:\xampp1\htdocs\TrailMateLaravel).
   Merge directories; do not replace your project's .env, composer.json, vendor/,
   bootstrap/, or other Laravel core files.
4. Ensure your .env database values match your local MySQL database, e.g.:

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=trailmate
   DB_USERNAME=root
   DB_PASSWORD=

   Create the database in phpMyAdmin first if it does not exist. Use your own
   database credentials; do not assume root has an empty password.
5. From the project root run:

   php artisan optimize:clear
   php artisan migrate
   php artisan route:list
   php artisan serve

6. Open http://127.0.0.1:8000 (or the URL shown by artisan serve).

CHECKLIST
---------
[ ] Home page loads.
[ ] Explore page lists trails; search and difficulty filter update results without
    a full reload.
[ ] Add a trail; validation errors appear inline; successful save redirects.
[ ] Open a trail, toggle favourite, and verify /favourites.
[ ] Submit a review and verify it appears without reloading.
[ ] Edit and delete a trail.
[ ] Test weather after allowing browser location permission.
[ ] Test at narrow mobile width.

DATABASE TABLES
---------------
trails: name, location, difficulty, distance_km, description, timestamps
reviews: trail_id, reviewer_name, rating, comment, timestamps

SECURITY BEFORE PUBLIC DEPLOYMENT
---------------------------------
Add authentication (Laravel Breeze is already compatible with a standard Laravel
project), authorize trail management through policies or middleware, add rate
limits to public review endpoints, configure HTTPS, set APP_DEBUG=false, use a
strong APP_KEY, validate deployment environment settings, and use least-privilege
database credentials. Never commit .env or API secrets.

PROFESSIONAL AUTHENTICATION + TRAIL OWNERSHIP ADDITIONS
-------------------------------------------------------
This enhanced package adds visible Log in / Sign up / Log out navigation using
Laravel Breeze's named routes. Your existing project must have Breeze installed
and routes/auth.php present. If not, install/configure Breeze before using these
links; the UI cannot provide secure authentication by itself.

A migration adds nullable user_id ownership to trails. New trails are assigned
to the signed-in user. Users can edit/delete their own trails; users with role
'admin' can manage all trails. Existing trails with no owner can be managed by
admins. Ensure your users table has a role column if you intend to use admin
accounts; this project already had a role migration in the prior setup notes.

After merging, run:
  C:\xampp1\php\php.exe artisan optimize:clear
  C:\xampp1\php\php.exe artisan migrate
  C:\xampp1\php\php.exe artisan route:list
  C:\xampp1\php\php.exe artisan serve

To make an account an administrator, update that account's role to 'admin' in
your database only after verifying that the role column exists. Never expose
admin registration to the public. Test ownership and access denial using two
different user accounts before deployment.

ADMIN DASHBOARD ADDITIONS (UPDATED PACKAGE)
------------------------------------------
This version includes AdminMiddleware, a protected /admin dashboard, user list,
all-trails management, review moderation, and a safe migration that adds users.role
only if the column is missing. It also defines the named /dashboard route expected
by Laravel Breeze; admins are redirected to /admin and ordinary users to the home page.

After merging, run these commands from the project root:
  C:\xampp1\php\php.exe artisan optimize:clear
  C:\xampp1\php\php.exe artisan migrate
  C:\xampp1\php\php.exe artisan route:list
  C:\xampp1\php\php.exe artisan serve

Then promote the intended existing account in Tinker (use your actual registered email):
  C:\xampp1\php\php.exe artisan tinker
  $user = App\Models\User::where('email', 'YOUR_EMAIL')->first();
  $user->role = 'admin';
  $user->save();
  $user->fresh()->role;
  exit

Visit /login, then /admin. Admin middleware checks the role on every protected request.
Test with a separate normal account: /admin should return 403. Never enable public admin
registration. The project overlay is not a full Laravel installation and must be merged
into the Laravel project you already created; it does not include vendor/ or .env.
