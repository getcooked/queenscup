<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Consumed by the Android customer app and by the customer side of the web
| app, so both go through exactly the same rules. Counter-side endpoints live
| in web.php because the admin panel authenticates with a session.
|
| Reserving does not require an account: a guest reserves and then tracks the
| order with the reference code returned here.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Menu
    Route::get('/products', [ProductController::class, 'index']);

    // Accounts are optional; they only persist a customer's history.
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // Reserving and tracking. The reference code is the secret, so tracking
    // stays open while still being unguessable.
    Route::post('/reservations/quote', [ReservationController::class, 'quote']);
    Route::post('/reservations', [ReservationController::class, 'store'])->middleware('throttle:30,1');
    Route::get('/reservations/{reference}', [ReservationController::class, 'show']);
    Route::post('/reservations/{reference}/cancel', [ReservationController::class, 'cancel']);

    // Push registration works for guests too, keyed to the reference.
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
    Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/my/reservations', [ReservationController::class, 'mine']);
    });
});
