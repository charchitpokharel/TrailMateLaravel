<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Laravel Breeze redirects to the named dashboard route after login/registration.
// Administrators go to /admin; ordinary users go to the public home page.
Route::get('/dashboard', function () {
    return auth()->user()?->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('home');
})->middleware('auth')->name('dashboard');

// Admin-only pages are protected server-side by authentication and role middleware.
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/trails', [AdminDashboardController::class, 'trails'])->name('trails');
    Route::get('/reviews', [AdminDashboardController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/{review}', [AdminDashboardController::class, 'destroyReview'])->name('reviews.destroy');
});

// Public browsing; management actions require an authenticated account.
Route::get('/trails', [TrailController::class, 'index'])->name('trails.index');
Route::get('/trails/create', [TrailController::class, 'create'])->middleware('auth')->name('trails.create');
Route::post('/trails', [TrailController::class, 'store'])->middleware('auth')->name('trails.store');
Route::get('/trails/{trail}', [TrailController::class, 'show'])->whereNumber('trail')->name('trails.show');
Route::get('/trails/{trail}/edit', [TrailController::class, 'edit'])->middleware('auth')->whereNumber('trail')->name('trails.edit');
Route::put('/trails/{trail}', [TrailController::class, 'update'])->middleware('auth')->whereNumber('trail')->name('trails.update');
Route::patch('/trails/{trail}', [TrailController::class, 'update'])->middleware('auth')->whereNumber('trail')->name('trails.update.patch');
Route::delete('/trails/{trail}', [TrailController::class, 'destroy'])->middleware('auth')->whereNumber('trail')->name('trails.destroy');

Route::get('/api/trails/search', [TrailController::class, 'search'])->name('api.trails.search');

Route::get('/weather', [WeatherController::class, 'index'])->name('weather');
Route::get('/api/weather', [WeatherController::class, 'forecast'])->name('api.weather');

Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites');
Route::post('/api/favourites/{trail}', [FavouriteController::class, 'toggle'])->name('api.favourites.toggle');

Route::get('/reviews/{trail}', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('/api/reviews/{trail}', [ReviewController::class, 'store'])->name('api.reviews.store');

// Laravel Breeze authentication routes (login, registration, password reset, logout).
if (file_exists(__DIR__.'/auth.php')) {
    require __DIR__.'/auth.php';
}
