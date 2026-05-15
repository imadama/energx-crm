<?php

use App\Http\Controllers\AanvraagController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiFieldController;
use App\Http\Controllers\KlantController;
use App\Http\Controllers\OfferteController;
use App\Http\Controllers\OfferteSectieController;
use App\Http\Controllers\OfferteTemplateController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));
Route::get('/api/docs', fn () => view('docs.swagger'))->name('docs.swagger');

use App\Http\Controllers\ContactpersoonController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('klanten', KlantController::class)->parameters(['klanten' => 'klant']);
    
    // Contactpersonen routes
    Route::get('klanten/{klant}/contactpersonen/create', [ContactpersoonController::class, 'create'])->name('contactpersonen.create');
    Route::post('klanten/{klant}/contactpersonen', [ContactpersoonController::class, 'store'])->name('contactpersonen.store');
    Route::get('contactpersonen/{contactpersoon}/edit', [ContactpersoonController::class, 'edit'])->name('contactpersonen.edit');
    Route::put('contactpersonen/{contactpersoon}', [ContactpersoonController::class, 'update'])->name('contactpersonen.update');
    Route::delete('contactpersonen/{contactpersoon}', [ContactpersoonController::class, 'destroy'])->name('contactpersonen.destroy');

    // Tickets routes
    Route::resource('tickets', App\Http\Controllers\TicketController::class)->except(['edit', 'destroy']);
    Route::post('tickets/{ticket}/reacties', [App\Http\Controllers\TicketReactieController::class, 'store'])->name('ticket-reacties.store');

    Route::resource('producten', ProductController::class)->parameters(['producten' => 'product']);
    Route::patch('producten-volgorde', [ProductController::class, 'updateOrder'])->name('producten.order.update');
    Route::get('aanvragen', [AanvraagController::class, 'index'])->name('aanvragen.index');
    Route::patch('aanvragen/{aanvraag}/status', [AanvraagController::class, 'updateStatus'])->name('aanvragen.status');
    Route::resource('offertes', OfferteController::class);
    Route::get('offertes/{offerte}/editor', [OfferteController::class, 'editor'])->name('offertes.editor');
    Route::patch('offertes/{offerte}/regels', [OfferteController::class, 'updateRegels'])->name('offertes.regels.update');
    Route::post('offertes/{offerte}/secties', [OfferteSectieController::class, 'store'])->name('offertes.secties.store');
    Route::patch('offertes/{offerte}/secties/{sectie}', [OfferteSectieController::class, 'update'])->name('offertes.secties.update');
    Route::delete('offertes/{offerte}/secties/{sectie}', [OfferteSectieController::class, 'destroy'])->name('offertes.secties.destroy');
    Route::patch('offertes/{offerte}/secties-volgorde', [OfferteSectieController::class, 'reorder'])->name('offertes.secties.reorder');
    Route::resource('offerte-templates', OfferteTemplateController::class)->except('show');
    Route::resource('api-fields', ApiFieldController::class)->except('show');
    Route::get('api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
});

// Publieke offerte viewer (geen login nodig)
Route::get('/offerte/{token}', [OfferteController::class, 'viewer'])->name('offertes.viewer');
Route::get('/offerte/{token}/pdf', [OfferteController::class, 'pdf'])->name('offertes.pdf');
Route::post('/offerte/{token}/accepteer', [OfferteController::class, 'accepteer'])->name('offertes.accepteer');

require __DIR__.'/auth.php';
