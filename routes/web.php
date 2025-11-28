<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderRequestController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    // User Order Routes
    Route::get('/my-orders', [OrderController::class, 'userIndex'])->name('user.orders.index');
    Route::get('/my-orders/create', [OrderController::class, 'userCreate'])->name('user.orders.create');
    Route::post('/my-orders', [OrderController::class, 'userStore'])->name('user.orders.store');
    Route::get('/my-orders/{order}', [OrderController::class, 'userShow'])->name('user.orders.show');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin',
])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');

    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('admin.orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');  

    Route::get('/order-requests', [OrderRequestController::class, 'index'])->name('admin.order_request.index');
    Route::get('/order-requests/create', [OrderRequestController::class, 'create'])->name('admin.order_request.create');
    Route::post('/order-requests', [OrderRequestController::class, 'store'])->name('admin.order_request.store');
    Route::get('/order-requests/{orderRequest}', [OrderRequestController::class, 'show'])->name('admin.order_request.show');
    Route::get('/order-requests/{orderRequest}/edit', [OrderRequestController::class, 'edit'])->name('admin.order_request.edit');
    Route::put('/order-requests/{orderRequest}', [OrderRequestController::class, 'update'])->name('admin.order_request.update');
    Route::post('/order-requests/{orderRequest}/approve', [OrderRequestController::class, 'approve'])->name('admin.order_request.approve');
    Route::post('/order-requests/{orderRequest}/decline', [OrderRequestController::class, 'decline'])->name('admin.order_request.decline');
    Route::delete('/order-requests/{orderRequest}', [OrderRequestController::class, 'destroy'])->name('admin.order_request.destroy');
});


