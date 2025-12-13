<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\NotificationController;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'prevent-back-history',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('dashboard');

    // User Order Routes
    Route::get('/my-orders', [OrderController::class, 'userIndex'])->name('user.orders.index');
    Route::get('/my-orders/create', [OrderController::class, 'userCreate'])->name('user.orders.create');
    Route::post('/my-orders', [OrderController::class, 'userStore'])->name('user.orders.store');
    Route::get('/my-orders/{order}', [OrderController::class, 'userShow'])->name('user.orders.show');
    Route::put('/my-orders/{order}/cancel', [OrderController::class, 'userCancel'])->name('user.orders.cancel');
    Route::get('/my-orders/{order}/receipt', [OrderController::class, 'downloadReceipt'])->name('user.orders.receipt');

    // Notification Routes (User)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/check-new', [NotificationController::class, 'checkNew'])->name('notifications.check-new');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin',
    'prevent-back-history',
])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('admin.customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/admin/customers/{customer}', [CustomerController::class, 'destroy'])->name('admin.customers.destroy');
    
    // Route for viewing user customers
    Route::get('/customers/user/{user}', [CustomerController::class, 'showUser'])->name('admin.customers.show-user');
    
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('admin.orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');

    // Reports
    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('admin.reports');  
    // Add these routes for the calculation functionality
    Route::post('/admin/orders/calculate', [OrderController::class, 'calculate'])->name('admin.orders.calculate');
    Route::post('/user/orders/calculate', [OrderController::class, 'userCalculate'])->name('user.orders.calculate');

    // Order approval routes
    Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('admin.orders.approve');
    Route::post('/orders/{order}/decline', [OrderController::class, 'decline'])->name('admin.orders.decline');
    
    // Laundry workflow routes
    Route::post('/orders/{order}/confirm-weight', [OrderController::class, 'confirmWeight'])->name('admin.orders.confirm-weight');
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
    Route::post('/orders/{order}/start-washing', [OrderController::class, 'startWashing'])->name('admin.orders.start-washing');
    Route::post('/orders/{order}/start-drying', [OrderController::class, 'startDrying'])->name('admin.orders.start-drying');
    Route::post('/orders/{order}/record-payment', [OrderController::class, 'recordPayment'])->name('admin.orders.record-payment');
    
    // Pending orders page
    Route::get('/orders/pending', [OrderController::class, 'pending'])->name('admin.orders.pending');
    
    // Machine Management
    Route::get('/machines/dashboard', [MachineController::class, 'dashboard'])->name('machines.dashboard');
    Route::post('/orders/{order}/assign-washer', [MachineController::class, 'assignWasher'])->name('machines.assign-washer');
    Route::post('/orders/{order}/assign-dryer', [MachineController::class, 'assignDryer'])->name('machines.assign-dryer');
    Route::post('/machines/check-completed', [MachineController::class, 'checkCompletedMachines'])->name('machines.check-completed');
});


