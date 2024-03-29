<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\OrderItemController;
use App\Http\Controllers\api\ProductController;

Route::GET('products', [ProductController::class, 'index']);
Route::GET('orders', [OrderController::class, 'index']);

Route::middleware(['isNotEmployee'])->group(function () {
    Route::POST('login', [AuthController::class, 'login']);
    Route::POST('register', [AuthController::class, 'register']);
    Route::POST('orders', [OrderController::class, 'store']);
    Route::PATCH('orders/{order}/delivered', [OrderController::class, 'update_delivered']);  // um bocado aldrabado porque no PickupOrder não sei como guardar o delivered_by (a menos que criasse uma variavel na DB mas não sei se podia fazer isso)
});

Route::middleware('auth:api')->group(function () {
    Route::POST('logout', [AuthController::class, 'logout']);
    Route::GET('users/me', [UserController::class, 'show_me']);

    Route::middleware(['isManager'])->group(function () {
        Route::GET('users', [UserController::class, 'index']);
        Route::POST('users', [UserController::class, 'store']);

        Route::POST('products', [ProductController::class, 'store']);
        Route::PUT('products/{product}', [ProductController::class, 'update']);
        Route::GET('products/{product}', [ProductController::class, 'show']);
        Route::DELETE('products/{product}', [ProductController::class, 'destroy']);
    });

    Route::middleware(['isEmployee'])->group(function () {
        Route::PATCH('orders/{order}/ready', [OrderController::class, 'update_ready']);
        Route::PATCH('orders/{order}/cancelled', [OrderController::class, 'update_cancelled']);
    });

    Route::middleware(['isChef'])->group(function () {
        Route::PATCH('order_items/{orderItem}/preparing', [OrderItemController::class, 'update_preparing']);
        Route::PATCH('order_items/{orderItem}/ready', [OrderItemController::class, 'update_ready']);
    });

    Route::GET('users/{user}', [UserController::class, 'show'])
        ->middleware('can:view,user');
    Route::PUT('users/{user}', [UserController::class, 'update'])
        ->middleware('can:update,user');
    Route::DELETE('users/{user}', [UserController::class, 'destroy'])
        ->middleware('can:delete,user');
    Route::PATCH('users/{user}/password', [UserController::class, 'update_password'])
        ->middleware('can:updatePassword,user');
    Route::PATCH('users/{user}/blocked', [UserController::class, 'update_blocked'])
        ->middleware('can:updateBlocked,user');

    Route::GET('orders/{order}', [OrderController::class, 'show'])
        ->middleware('can:view,order');
    Route::GET('users/{user}/orders', [OrderController::class, 'getOrdersOfUser']);
    //Route::get('customers/{customer}/orders/forpickup', [OrderController::class, 'getOrdersOfCustomerForPickup']);*/
});
