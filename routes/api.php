<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ChatController;
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
| Reserving requires an account. Tracking remains available by reference.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Menu
    Route::get('/products', [ProductController::class, 'index']);

    // Customers sign in before placing a reservation.
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/auth/verify', [AuthController::class, 'verify'])->middleware('throttle:10,1');
    Route::post('/auth/resend', [AuthController::class, 'resend'])->middleware('throttle:5,1');

    // Reserving and tracking. The reference code is the secret, so tracking
    // stays open while still being unguessable.
    // The assistant. Open to anyone, but a token identifies the customer so
    // the phone sees the same conversation as the website.
    Route::get('/chat', [ChatController::class, 'history'])->middleware('throttle:30,1');
    Route::post('/chat', [ChatController::class, 'send'])->middleware('throttle:30,1');

    Route::post('/reservations/quote', [ReservationController::class, 'quote'])->middleware('throttle:30,1');
    Route::post('/reservations', [ReservationController::class, 'store'])->middleware(['auth:sanctum', 'throttle:30,1']);
    // Tighter limits on reference lookups slow down guessing references.
    Route::get('/reservations/{reference}', [ReservationController::class, 'show'])->middleware('throttle:20,1');
    Route::post('/reservations/{reference}/cancel', [ReservationController::class, 'cancel'])->middleware('throttle:10,1');

    // Push registration works for guests too, keyed to the reference.
    Route::post('/device-tokens', [DeviceTokenController::class, 'store'])->middleware('throttle:20,1');
    Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy'])->middleware('throttle:20,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/my/reservations', [ReservationController::class, 'mine']);
    });
});
