
<?php

use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// Weather page
Route::get('/weather', [WeatherController::class, 'index'])
    ->name('weather.index');

// Weather forecast endpoint
Route::get('/api/weather', [WeatherController::class, 'forecast'])
    ->middleware('throttle:30,1')
    ->name('api.weather');

// Live trail search
Route::get('/api/trails/search', [TrailController::class, 'search'])
    ->middleware('throttle:60,1')
    ->name('api.trails.search');

// Admin-only features
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/trails/create', [TrailController::class, 'create'])
        ->name('trails.create');

    Route::post('/trails', [TrailController::class, 'store'])
        ->name('trails.store');

    Route::get('/trails/{trail}/edit', [TrailController::class, 'edit'])
        ->name('trails.edit');

    Route::put('/trails/{trail}', [TrailController::class, 'update'])
        ->name('trails.update');

    Route::delete('/trails/{trail}', [TrailController::class, 'destroy'])
        ->name('trails.destroy');
});

// Public trail browsing
Route::get('/trails', [TrailController::class, 'index'])
    ->name('trails.index');

Route::get('/trails/{trail}', [TrailController::class, 'show'])
    ->whereNumber('trail')
    ->name('trails.show');

// Authenticated user features
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    // User profile
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Favourites
    Route::get('/favourites', [FavouriteController::class, 'index'])
        ->name('favourites.index');

    Route::post('/favourites/{trail}', [FavouriteController::class, 'store'])
        ->name('favourites.store');

    Route::delete('/favourites/{trail}', [FavouriteController::class, 'destroy'])
        ->name('favourites.destroy');

    // Reviews
    Route::post('/trails/{trail}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');
});

// Authentication routes
require __DIR__.'/auth.php';
