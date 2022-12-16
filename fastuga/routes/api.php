<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\OrderController;
use App\Http\Controllers\api\ProductController;

Route::POST('login', [AuthController::class, 'login']);
Route::POST('register', [AuthController::class, 'register']);
Route::GET('products', [ProductController::class, 'index']);
Route::POST('products', [ProductController::class, 'store']);

Route::middleware('auth:api')->group(function () {
    Route::POST('logout', [AuthController::class, 'logout']);
    Route::GET('users/me', [UserController::class, 'show_me']);

    Route::middleware(['isManager'])->group(function () {
        Route::GET('users', [UserController::class, 'index']);
        Route::POST('users', [UserController::class, 'store']);


        Route::PUT('products/{product}', [ProductController::class, 'update']);
        Route::GET('products/{product}', [ProductController::class, 'show']);
        Route::DELETE('products/{product}', [ProductController::class, 'destroy']);
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


    /*Route::get('users/{user}/tasks', [TaskController::class, 'getTasksOfUser']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::post('tasks', [TaskController::class, 'store']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
    Route::put('tasks/{task}', [TaskController::class, 'update']);
    Route::patch('tasks/{task}/completed', [TaskController::class, 'update_completed']);*/



    Route::GET('orders', [OrderController::class, 'index']);
    Route::GET('orders/{order}', [OrderController::class, 'show']);
    Route::GET('orders/{order}/products', [OrderController::class, 'showWithProducts']);
    Route::POST('orders', [OrderController::class, 'store']);
    Route::DELETE('orders/{order}', [OrderController::class, 'destroy']);
    Route::PUT('orders/{order}', [OrderController::class, 'update']);
    Route::GET('users/{user}/orders', [OrderController::class, 'getOrdersOfUser']);
    Route::PATCH('orders/{order}/completed', [OrderController::class, 'update_completed']);
    //Route::get('customers/{customer}/orders/forpickup', [OrderController::class, 'getOrdersOfCustomerForPickup']);
});
