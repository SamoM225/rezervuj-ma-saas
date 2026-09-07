<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Route;

// Optional external booking-widget API, scoped per tenant: /api/{slug}/…
// Administrative actions intentionally remain only on the session-protected web routes.
Route::prefix('{tenant}')
    ->where(['tenant' => config('tenancy.slug_pattern')])
    ->middleware('tenant')
    ->group(function () {
        Route::middleware('throttle:public-api')->group(function () {
            Route::get('/cities', [WorkerController::class, 'getCities']);
            Route::get('/categories/{cityId?}', [CategoryController::class, 'index']);
            Route::get('/services', [ServiceController::class, 'index']);
            Route::get('/workers/{categoryId?}', [WorkerController::class, 'getWorkers']);
            Route::get('/workers-by-service/{serviceId}', [WorkerController::class, 'getWorkersByService']);
            Route::get('/available-dates/{workerId}', [BookingController::class, 'getAvaiableDates']);
        });

        Route::post('/bookings', [BookingController::class, 'store'])
            ->middleware(['antibot', 'blacklist', 'throttle:booking']);
    });
