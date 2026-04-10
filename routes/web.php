<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentAreaController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\ExcerptController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\ImageUploadController;
use App\Http\Controllers\Admin\IncidentController as AdminIncidentController;
use App\Http\Controllers\Admin\IncidentTypeController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RFC2350Controller;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('landing');
Route::get('/profile', ProfileController::class)->name('profile.show');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::post('/posts/{post}/ratings', [RatingController::class, 'store'])->name('posts.ratings.store');
Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documents/{document:slug}/view', [DocumentController::class, 'view'])->name('documents.view');
Route::get('/documents/{document:slug}/download', [DocumentController::class, 'download'])->name('documents.download');
Route::get('/rfc2350', [RFC2350Controller::class, 'index'])->name('rfc2350.index');
Route::get('/rfc2350/view', [RFC2350Controller::class, 'view'])->name('rfc2350.view');
Route::get('/rfc2350/download', [RFC2350Controller::class, 'download'])->name('rfc2350.download');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::get('/faq/search', [FaqController::class, 'search'])->name('faq.search');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/incident', [IncidentController::class, 'create'])->name('incident.create');
Route::post('/incident', [IncidentController::class, 'store'])->middleware('throttle:incident-create')->name('incident.store');
Route::post('/incidents/search', [IncidentController::class, 'search'])->middleware('throttle:incident-search')->name('incident.search');
Route::get('/incidents/{caseId}/attachment', [IncidentController::class, 'downloadAttachment'])->middleware(['signed', 'throttle:incident-download'])->name('incident.attachment.download');
Route::get('/incidents/{caseId}', [IncidentController::class, 'showWithToken'])->middleware('throttle:incident-search')->name('incident.show');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

  Route::resource('incidents', AdminIncidentController::class);
  Route::put('/incidents/{incident}/management', [AdminIncidentController::class, 'updateManagement'])->name('incidents.management.update');
  Route::post('/incidents/{incident}/logs', [AdminIncidentController::class, 'addLog'])->name('incidents.logs.store');
  Route::resource('incident-types', IncidentTypeController::class);

  Route::resource('posts', AdminPostController::class)->except(['show']);
  Route::post('/images/upload', [ImageUploadController::class, 'store'])->name('images.upload');
  Route::post('/generate-excerpt', [ExcerptController::class, 'generate'])->name('generate-excerpt');

  Route::get('/taxonomy', [TaxonomyController::class, 'index'])->name('taxonomy.index');
  Route::post('/categories', [TaxonomyController::class, 'storeCategory'])->name('categories.store');
  Route::put('/categories/{category}', [TaxonomyController::class, 'updateCategory'])->name('categories.update');
  Route::delete('/categories/{category}', [TaxonomyController::class, 'destroyCategory'])->name('categories.destroy');
  Route::post('/tags', [TaxonomyController::class, 'storeTag'])->name('tags.store');
  Route::put('/tags/{tag}', [TaxonomyController::class, 'updateTag'])->name('tags.update');
  Route::delete('/tags/{tag}', [TaxonomyController::class, 'destroyTag'])->name('tags.destroy');

  Route::resource('document-areas', DocumentAreaController::class);
  Route::patch('/documents/{document}/toggle-visibility', [AdminDocumentController::class, 'toggleVisibility'])->name('documents.toggle-visibility');
  Route::resource('documents', AdminDocumentController::class);
  Route::resource('services', AdminServiceController::class);
  Route::resource('faqs', AdminFaqController::class)->except(['show', 'create', 'edit']);
  Route::resource('announcements', AnnouncementController::class)->except(['show', 'create', 'edit']);
  Route::resource('users', AdminUserController::class)->except(['show', 'create', 'edit'])->middleware('admin');
});

Route::middleware(['auth', 'verified'])->prefix('api')->name('api.')->group(function () {
  Route::get('/notifications/incidents', [NotificationController::class, 'getIncidentNotifications'])->name('notifications.incidents');
  Route::post('/notifications/{incident}/mark-read', [NotificationController::class, 'markAsRead'])->name('incidents.mark-read');
  Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

Route::middleware('auth')->group(function () {
  Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


require __DIR__.'/auth.php';
