<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StockTransactionController;
use App\Http\Controllers\Manajer\DashboardController as ManajerDashboardController;
use App\Http\Controllers\Manajer\StockController as ManajerStockController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\StockController as StaffStockController;
use App\Http\Controllers\NotificationController;

// Public Routes
Route::get('/', function () {
    return view('auth.login');
});

// Auth Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isManajer()) {
            return redirect()->route('manajer.dashboard');
        } elseif ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        }

        return redirect('/');
    })->name('dashboard');

    // Notification Routes (untuk semua role)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/get', [NotificationController::class, 'getNotifications'])->name('get');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/delete-all', [NotificationController::class, 'destroyAll'])->name('destroy-all');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('categories', CategoryController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Products
    Route::resource('products', ProductController::class);

    // Users
    Route::resource('users', UserController::class);

    // Stock Transactions
    Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [StockTransactionController::class, 'show'])->name('transactions.show');

    // Stock
    Route::get('/stock', function () {
        return view('admin.stock.index');
    })->name('stock.index');

    // Reports
    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('reports.index');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [\App\Http\Controllers\Admin\SettingController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [\App\Http\Controllers\Admin\SettingController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/app', [\App\Http\Controllers\Admin\SettingController::class, 'updateAppSettings'])->name('settings.app.update');
});

// Manajer Routes
Route::middleware(['auth', 'role:manajer gudang'])->prefix('manajer')->name('manajer.')->group(function () {
    Route::get('/dashboard', [ManajerDashboardController::class, 'index'])->name('dashboard');

    // Products (Read Only)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

    // Categories (Read Only)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Suppliers (Read Only)
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

    // Stock Management
    Route::get('/stock', [ManajerStockController::class, 'index'])->name('stock.index');
    Route::post('/stock/in', [ManajerStockController::class, 'storeIn'])->name('stock.in.store');
    Route::post('/stock/out', [ManajerStockController::class, 'storeOut'])->name('stock.out.store');
    Route::post('/stock/{id}/approve', [ManajerStockController::class, 'approve'])->name('stock.approve');
    Route::post('/stock/{id}/reject', [ManajerStockController::class, 'reject'])->name('stock.reject');

    // Transactions
    Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions.index');

    // Reports
    Route::get('/reports', function () {
        return view('manajer.reports.index');
    })->name('reports.index');

    // Approval
    Route::get('/approval', function () {
        return view('manajer.approval.index');
    })->name('approval.index');

    // Staff
    Route::get('/staff', function () {
        return view('manajer.staff.index');
    })->name('staff.index');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Manajer\ProfileController::class, 'index'])->name('profile');
});

// Staff Routes
Route::middleware(['auth', 'role:staff gudang'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Stock Operations
    Route::get('/stock/in', [StaffStockController::class, 'in'])->name('stock.in');
    Route::post('/stock/in', [StaffStockController::class, 'storeIn'])->name('stock.in.store');

    Route::get('/stock/out', [StaffStockController::class, 'out'])->name('stock.out');
    Route::post('/stock/out', [StaffStockController::class, 'storeOut'])->name('stock.out.store');

    Route::get('/stock/check', [StaffStockController::class, 'check'])->name('stock.check');

    // Products (Read Only)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');

    // Transactions
    Route::get('/transactions', function () {
        return view('staff.transactions.index');
    })->name('transactions.index');

    // Notifications
    Route::get('/notifications', function () {
        return view('staff.notifications.index');
    })->name('notifications.index');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Staff\ProfileController::class, 'index'])->name('profile');
});

require __DIR__.'/auth.php';
