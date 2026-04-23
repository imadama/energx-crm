<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KlantController;
use App\Http\Controllers\OfferteController;
use App\Http\Controllers\OfferteSectieController;
use App\Http\Controllers\OfferteTemplateController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('klanten', KlantController::class)->parameters(['klanten' => 'klant']);
    Route::resource('producten', ProductController::class)->parameters(['producten' => 'product']);
    Route::resource('offertes', OfferteController::class);
    Route::get('offertes/{offerte}/editor', [OfferteController::class, 'editor'])->name('offertes.editor');
    Route::patch('offertes/{offerte}/regels', [OfferteController::class, 'updateRegels'])->name('offertes.regels.update');
    Route::post('offertes/{offerte}/secties', [OfferteSectieController::class, 'store'])->name('offertes.secties.store');
    Route::patch('offertes/{offerte}/secties/{sectie}', [OfferteSectieController::class, 'update'])->name('offertes.secties.update');
    Route::delete('offertes/{offerte}/secties/{sectie}', [OfferteSectieController::class, 'destroy'])->name('offertes.secties.destroy');
    Route::patch('offertes/{offerte}/secties-volgorde', [OfferteSectieController::class, 'reorder'])->name('offertes.secties.reorder');
    Route::resource('offerte-templates', OfferteTemplateController::class)->except('show');
});

// Publieke offerte viewer (geen login nodig)
Route::get('/offerte/{token}', [OfferteController::class, 'viewer'])->name('offertes.viewer');
Route::post('/offerte/{token}/accepteer', [OfferteController::class, 'accepteer'])->name('offertes.accepteer');

require __DIR__.'/auth.php';
