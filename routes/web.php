<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StockTransactionController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Manajer\DashboardController as ManajerDashboardController;
use App\Http\Controllers\Manajer\StockController as ManajerStockController;
use App\Http\Controllers\Manajer\SupplierController as ManajerSupplierController;
use App\Http\Controllers\Manajer\ProductController as ManajerProductController;
use App\Http\Controllers\Manajer\ApprovalController as ManajerApprovalController;
use App\Http\Controllers\Manajer\ReportController as ManajerReportController;
use App\Http\Controllers\Manajer\StockOpnameController as ManajerStockOpnameController;
use App\Http\Controllers\Manajer\TransactionController as ManajerTransactionController;
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
        Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
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
    Route::get('/products/approval', [ProductController::class, 'approval'])->name('products.approval');
    Route::get('/products/import-form', [ProductController::class, 'importForm'])->name('products.import-form');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products/download-template', [ProductController::class, 'downloadTemplate'])->name('products.download-template');
    Route::resource('products', ProductController::class);
    Route::post('/products/{id}/approve', [ProductController::class, 'approve'])->name('products.approve');
    Route::post('/products/{id}/reject', [ProductController::class, 'reject'])->name('products.reject');

    // Product Attributes
    Route::resource('attributes', \App\Http\Controllers\Admin\AttributeController::class)->except(['show']);

    // Users
    Route::resource('users', UserController::class);

    // Stock Transactions
    Route::resource('stock-transactions', StockTransactionController::class);
    Route::post('/stock-transactions/{id}/approve', [StockTransactionController::class, 'approve'])->name('stock-transactions.approve');
    Route::post('/stock-transactions/{id}/reject', [StockTransactionController::class, 'reject'])->name('stock-transactions.reject');

    // Transactions alias (for menu compatibility)
    Route::get('/transactions', [StockTransactionController::class, 'index'])->name('transactions.index');

    // Stock Report
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/stock', [ReportController::class, 'exportStock'])->name('reports.export.stock');
    Route::get('/reports/export/transactions', [ReportController::class, 'exportTransactions'])->name('reports.export.transactions');

    // Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{id}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::post('/activity-logs/cleanup', [\App\Http\Controllers\Admin\ActivityLogController::class, 'cleanup'])->name('activity-logs.cleanup');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [\App\Http\Controllers\Admin\SettingController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [\App\Http\Controllers\Admin\SettingController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/app', [\App\Http\Controllers\Admin\SettingController::class, 'updateAppSettings'])->name('settings.app.update');
});

// Manajer Routes
Route::middleware(['auth', 'role:manajer gudang'])->prefix('manajer')->name('manajer.')->group(function () {
    Route::get('/dashboard', [ManajerDashboardController::class, 'index'])->name('dashboard');

    // Products (Read Only + Create New with Approval)
    Route::get('/products', [ManajerProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ManajerProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ManajerProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}', [ManajerProductController::class, 'show'])->name('products.show');

    // Categories (Read Only)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Suppliers (Read Only)
    Route::get('/suppliers', [ManajerSupplierController::class, 'index'])->name('suppliers.index');

    // Stock Management
    Route::get('/stock', [ManajerStockController::class, 'index'])->name('stock.index');
    Route::post('/stock/in', [ManajerStockController::class, 'storeIn'])->name('stock.in.store');
    Route::post('/stock/out', [ManajerStockController::class, 'storeOut'])->name('stock.out.store');
    Route::post('/stock/{id}/approve', [ManajerStockController::class, 'approve'])->name('stock.approve');
    Route::post('/stock/{id}/reject', [ManajerStockController::class, 'reject'])->name('stock.reject');
    Route::post('/stock/assign-opname', [ManajerStockController::class, 'assignStockOpname'])->name('stock.assign-opname');

    // Transactions
    Route::get('/transactions', [ManajerTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [ManajerStockController::class, 'create'])->name('transactions.create');
    Route::post('/transactions/store', [ManajerStockController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{id}', [ManajerTransactionController::class, 'show'])->name('transactions.show');

    // Reports
    Route::get('/reports', [ManajerReportController::class, 'index'])->name('reports.index');

    // Approval
    Route::get('/approval', [ManajerApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/{id}/approve', [ManajerApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{id}/reject', [ManajerApprovalController::class, 'reject'])->name('approval.reject');
    Route::post('/approval/opname/{id}/approve', [ManajerApprovalController::class, 'approveOpname'])->name('approval.opname.approve');
    Route::post('/approval/opname/{id}/reject', [ManajerApprovalController::class, 'rejectOpname'])->name('approval.opname.reject');

    // Stock Opname (Deprecated - merged with approval)
    // Route::get('/stock-opname', [ManajerStockOpnameController::class, 'index'])->name('stock-opname.index');
    // Route::post('/stock-opname/{id}/approve', [ManajerStockOpnameController::class, 'approve'])->name('stock-opname.approve');
    // Route::post('/stock-opname/{id}/reject', [ManajerStockOpnameController::class, 'reject'])->name('stock-opname.reject');

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

    // Stock Confirmation
    Route::post('/stock/confirm/{id}', [StaffStockController::class, 'confirm'])->name('stock.confirm');

    // Stock Opname (Input by Staff)
    Route::get('/stock-opname', [\App\Http\Controllers\Staff\StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::post('/stock-opname', [\App\Http\Controllers\Staff\StockOpnameController::class, 'store'])->name('stock-opname.store');
    Route::get('/stock-opname/history', [\App\Http\Controllers\Staff\StockOpnameController::class, 'history'])->name('stock-opname.history');

    // Products (Read Only)
    Route::get('/products', [\App\Http\Controllers\Staff\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [\App\Http\Controllers\Staff\ProductController::class, 'show'])->name('products.show');

    // Transactions
    Route::get('/transactions', [\App\Http\Controllers\Staff\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}', [\App\Http\Controllers\Staff\TransactionController::class, 'show'])->name('transactions.show');

    // Notifications
    Route::get('/notifications', function () {
        return view('staff.notifications.index');
    })->name('notifications.index');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Staff\ProfileController::class, 'index'])->name('profile');
});

require __DIR__.'/auth.php';
