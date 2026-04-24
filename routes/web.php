<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KlantController;
use App\Http\Controllers\OfferteController;
use App\Http\Controllers\OfferteTemplateController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('klanten', KlantController::class)->parameters(['klanten' => 'klant']);
    Route::resource('producten', ProductController::class)->parameters(['producten' => 'product']);

    // Offertes
    Route::resource('offertes', OfferteController::class);
    Route::get('offertes/{offerte}/editor', [OfferteController::class, 'editor'])->name('offertes.editor');
    Route::patch('offertes/{offerte}/document', [OfferteController::class, 'updateDocument'])->name('offertes.document.update');

    // Prijstabel regels
    Route::post('offertes/{offerte}/regels', [OfferteController::class, 'storeRegel'])->name('offertes.regels.store');
    Route::patch('offertes/{offerte}/regels/{regel}', [OfferteController::class, 'updateRegel'])->name('offertes.regels.update');
    Route::delete('offertes/{offerte}/regels/{regel}', [OfferteController::class, 'destroyRegel'])->name('offertes.regels.destroy');
    Route::patch('offertes/{offerte}/regels-volgorde', [OfferteController::class, 'reorderRegels'])->name('offertes.regels.reorder');

    // Afbeelding upload
    Route::post('upload-afbeelding', [OfferteController::class, 'uploadAfbeelding'])->name('upload.afbeelding');

    // Templates
    Route::resource('offerte-templates', OfferteTemplateController::class)->except('show');
    Route::get('offerte-templates/{offerteTemplate}/editor', [OfferteTemplateController::class, 'editor'])->name('offerte-templates.editor');
    Route::patch('offerte-templates/{offerteTemplate}/document', [OfferteTemplateController::class, 'updateDocument'])->name('offerte-templates.document.update');
});

// Publieke offerte viewer (geen login nodig)
Route::get('/offerte/{token}', [OfferteController::class, 'viewer'])->name('offertes.viewer');
Route::post('/offerte/{token}/accepteer', [OfferteController::class, 'accepteer'])->name('offertes.accepteer');

require __DIR__.'/auth.php';
