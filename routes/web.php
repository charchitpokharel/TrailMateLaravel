<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrailController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WeatherController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('trails', TrailController::class)->except(['destroy']);
Route::get('/trails/{trail}/edit', [TrailController::class, 'edit'])->name('trails.edit');
Route::put('/trails/{trail}', [TrailController::class, 'update'])->name('trails.update');
Route::delete('/trails/{trail}', [TrailController::class, 'destroy'])->name('trails.destroy');

Route::get('/api/trails/search', [TrailController::class, 'search'])->name('api.trails.search');
Route::get('/weather', [WeatherController::class, 'index'])->name('weather');
Route::get('/api/weather', [WeatherController::class, 'forecast'])->name('api.weather');

Route::get('/favourites', [FavouriteController::class, 'index'])->name('favourites');
Route::post('/api/favourites/{trail}', [FavouriteController::class, 'toggle'])->name('api.favourites.toggle');

Route::get('/reviews/{trail}', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('/api/reviews/{trail}', [ReviewController::class, 'store'])->name('api.reviews.store');
