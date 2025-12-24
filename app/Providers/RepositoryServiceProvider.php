<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Repositories\CategoryRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\ProductRepository;
use App\Repositories\StockTransactionRepository;
use App\Repositories\UserRepository;

// Services
use App\Services\CategoryService;
use App\Services\SupplierService;
use App\Services\ProductService;
use App\Services\StockTransactionService;
use App\Services\UserService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repositories
        $this->app->bind(CategoryRepository::class, function ($app) {
            return new CategoryRepository(new \App\Models\Category());
        });

        $this->app->bind(SupplierRepository::class, function ($app) {
            return new SupplierRepository(new \App\Models\Supplier());
        });

        $this->app->bind(ProductRepository::class, function ($app) {
            return new ProductRepository(new \App\Models\Product());
        });

        $this->app->bind(StockTransactionRepository::class, function ($app) {
            return new StockTransactionRepository(new \App\Models\StockTransaction());
        });

        $this->app->bind(UserRepository::class, function ($app) {
            return new UserRepository(new \App\Models\User());
        });

        // Bind Services
        $this->app->bind(CategoryService::class, function ($app) {
            return new CategoryService($app->make(CategoryRepository::class));
        });

        $this->app->bind(SupplierService::class, function ($app) {
            return new SupplierService($app->make(SupplierRepository::class));
        });

        $this->app->bind(ProductService::class, function ($app) {
            return new ProductService($app->make(ProductRepository::class));
        });

        $this->app->bind(StockTransactionService::class, function ($app) {
            return new StockTransactionService(
                $app->make(StockTransactionRepository::class),
                $app->make(ProductRepository::class)
            );
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(UserRepository::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
