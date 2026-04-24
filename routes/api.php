<?php

use App\Http\Controllers\Api\V1\OfferController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/offer', [OfferController::class, 'store'])->name('api.v1.offer.store');
});

