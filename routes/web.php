<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IncidentController as AdminIncidentController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('landing');
Route::get('/profil', ProfileController::class)->name('profil.show');
Route::get('/layanan', [ServiceController::class, 'index'])->name('services.index');
Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
Route::get('/berita/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/berita/kategori/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/kontak', [ContactController::class, 'index'])->name('contact.index');
Route::get('/insiden', [IncidentController::class, 'create'])->name('incident.create');
Route::post('/insiden', [IncidentController::class, 'store'])->name('incident.store');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
  Route::resource('incidents', AdminIncidentController::class)->except(['edit', 'update', 'destroy']);
  Route::resource('posts', AdminPostController::class)->except(['show']);

  // Routes for Taxonomy (Categories & Tags)
  Route::get('/taxonomy', [TaxonomyController::class, 'index'])->name('taxonomy.index');
  Route::post('/categories', [TaxonomyController::class, 'storeCategory'])->name('categories.store');
  Route::put('/categories/{category}', [TaxonomyController::class, 'updateCategory'])->name('categories.update');
  Route::delete('/categories/{category}', [TaxonomyController::class, 'destroyCategory'])->name('categories.destroy');
  Route::post('/tags', [TaxonomyController::class, 'storeTag'])->name('tags.store');
  Route::put('/tags/{tag}', [TaxonomyController::class, 'updateTag'])->name('tags.update');
  Route::delete('/tags/{tag}', [TaxonomyController::class, 'destroyTag'])->name('tags.destroy');
});

Route::middleware('auth')->group(function () {
  Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


require __DIR__.'/auth.php';
