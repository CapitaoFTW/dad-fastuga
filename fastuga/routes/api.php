<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\OrderController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users/me', [UserController::class, 'show_me']);

    Route::get('users', [UserController::class, 'index'])
        /*->middleware('can:viewAny,user')*/;
    Route::post('users', [UserController::class, 'store'])
        ->middleware('can:create,user');
    Route::get('users/{user}', [UserController::class, 'show'])
        ->middleware('can:view,user');
    Route::put('users/{user}', [UserController::class, 'update'])
        ->middleware('can:update,user');
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware('can:delete,user');
    Route::patch('users/{user}/password', [UserController::class, 'update_password'])
        ->middleware('can:updatePassword,user');

    /*Route::get('users/{user}/tasks', [TaskController::class, 'getTasksOfUser']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::post('tasks', [TaskController::class, 'store']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
    Route::put('tasks/{task}', [TaskController::class, 'update']);
    Route::patch('tasks/{task}/completed', [TaskController::class, 'update_completed']);*/

    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::get('orders/{order}/products', [OrderController::class, 'showWithProducts']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::delete('orders/{order}', [OrderController::class, 'destroy']);
    Route::put('orders/{order}', [OrderController::class, 'update']);
    //Route::get('customers/{customer}/orders', [OrderController::class, 'getOrdersOfCustomer']);
    //Route::get('customers/{customer}/orders/forpickup', [OrderController::class, 'getOrdersOfCustomerForPickup']);
});
