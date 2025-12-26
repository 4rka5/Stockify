<?php

namespace Database\Seeders;

use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $users = User::all();

        if ($products->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Please seed products and users first!');
            return;
        }

        $adminUser = $users->where('role', 'admin')->first() ?? $users->first();

        // Create some stock in transactions
        $inTransactions = [
            [
                'product_id' => $products->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'in',
                'quantity' => 50,
                'date' => Carbon::now()->subDays(10),
                'status' => 'diterima',
                'notes' => 'Pembelian stok awal dari supplier',
            ],
            [
                'product_id' => $products->skip(1)->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'in',
                'quantity' => 100,
                'date' => Carbon::now()->subDays(9),
                'status' => 'diterima',
                'notes' => 'Restock produk',
            ],
            [
                'product_id' => $products->skip(2)->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'in',
                'quantity' => 75,
                'date' => Carbon::now()->subDays(8),
                'status' => 'diterima',
                'notes' => 'Pembelian dari supplier baru',
            ],
            [
                'product_id' => $products->skip(3)->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'in',
                'quantity' => 200,
                'date' => Carbon::now()->subDays(7),
                'status' => 'diterima',
                'notes' => 'Stok masuk untuk produk populer',
            ],
        ];

        // Create some stock out transactions
        $outTransactions = [
            [
                'product_id' => $products->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'out',
                'quantity' => 10,
                'date' => Carbon::now()->subDays(5),
                'status' => 'dikeluarkan',
                'notes' => 'Penjualan ke customer',
            ],
            [
                'product_id' => $products->skip(1)->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'out',
                'quantity' => 25,
                'date' => Carbon::now()->subDays(4),
                'status' => 'dikeluarkan',
                'notes' => 'Penjualan grosir',
            ],
            [
                'product_id' => $products->skip(2)->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'out',
                'quantity' => 15,
                'date' => Carbon::now()->subDays(3),
                'status' => 'dikeluarkan',
                'notes' => 'Penjualan retail',
            ],
        ];

        // Create some pending transactions
        $pendingTransactions = [
            [
                'product_id' => $products->skip(4)->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'in',
                'quantity' => 50,
                'date' => Carbon::now()->subDays(1),
                'status' => 'pending',
                'notes' => 'Menunggu konfirmasi penerimaan dari supplier',
            ],
            [
                'product_id' => $products->skip(5)->first()->id,
                'user_id' => $adminUser->id,
                'type' => 'out',
                'quantity' => 20,
                'date' => Carbon::now(),
                'status' => 'pending',
                'notes' => 'Menunggu konfirmasi pengiriman',
            ],
        ];

        // Insert all transactions
        foreach (array_merge($inTransactions, $outTransactions, $pendingTransactions) as $transaction) {
            StockTransaction::create($transaction);
        }

        $this->command->info('Stock transactions seeded successfully!');
    }
}
