<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/changer-langue/{locale}', [PublicController::class, 'setLocale'])->name('locale');
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/a-propos', [PublicController::class, 'about'])->name('about');
Route::get('/formations', [PublicController::class, 'formations'])->name('formations');
Route::get('/formations/{formation:slug}', [PublicController::class, 'formation'])->name('formations.show');
Route::get('/admission-inscription', [PublicController::class, 'admission'])->name('admission');
Route::post('/admission-inscription', [PublicController::class, 'apply'])->name('apply');
Route::get('/preinscription-rapide', [PublicController::class, 'quickPreapply'])->name('preapply');
Route::post('/preinscription-rapide', [PublicController::class, 'storeQuickPreapply'])->name('preapply.store');
Route::get('/actualites', [PublicController::class, 'blog'])->name('blog');
Route::get('/actualites/{post:slug}', [PublicController::class, 'post'])->name('blog.show');
Route::get('/galerie', [PublicController::class, 'gallery'])->name('gallery');
Route::get('/entreprises-partenariats', [PublicController::class, 'partnerships'])->name('partnerships');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact', [PublicController::class, 'sendContact'])->name('contact.send');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/inscriptions', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/inscriptions/export/excel', [AdminApplicationController::class, 'exportExcel'])->name('applications.export.excel');
    Route::get('/inscriptions/export/pdf', [AdminApplicationController::class, 'exportPdf'])->name('applications.export.pdf');
    Route::get('/inscriptions/{application}/print', [AdminApplicationController::class, 'print'])->name('applications.print');
    Route::get('/inscriptions/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/inscriptions/{application}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::post('/inscriptions/{application}/assign', [AdminApplicationController::class, 'assign'])->name('applications.assign');
    Route::post('/inscriptions/{application}/comments', [AdminApplicationController::class, 'storeComment'])->name('applications.comments.store');
    Route::post('/inscriptions/{application}/follow-up', [AdminApplicationController::class, 'updateFollowUp'])->name('applications.followup');
    Route::post('/inscriptions/{application}/contacted', [AdminApplicationController::class, 'markContacted'])->name('applications.contacted');
    Route::get('/{resource}', [AdminController::class, 'index'])->name('resource');
    Route::post('/{resource}', [AdminController::class, 'store'])->name('resource.store');
    Route::post('/{resource}/{id}', [AdminController::class, 'update'])->name('resource.update.post');
    Route::put('/{resource}/{id}', [AdminController::class, 'update'])->name('resource.update');
    Route::delete('/{resource}/{id}', [AdminController::class, 'destroy'])->name('resource.destroy');
});

