<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        if ($categories->isEmpty() || $suppliers->isEmpty()) {
            $this->command->warn('Please seed categories and suppliers first!');
            return;
        }

        // Get the first available supplier
        $defaultSupplier = $suppliers->first()->id;

        // Helper function to get category or use first one
        $getCategoryId = function($name) use ($categories) {
            $category = $categories->where('name', $name)->first();
            return $category ? $category->id : $categories->first()->id;
        };

        $products = [
            [
                'category_id' => $getCategoryId('Elektronik'),
                'supplier_id' => $defaultSupplier,
                'name' => 'Laptop ASUS ROG Strix G15',
                'sku' => 'ELEC-LAP-001',
                'description' => 'Laptop gaming dengan prosesor Intel Core i7, RAM 16GB, RTX 3060',
                'purchase_price' => 15000000,
                'selling_price' => 18000000,
                'minimum_stock' => 5,
            ],
            [
                'category_id' => $getCategoryId('Elektronik'),
                'supplier_id' => $defaultSupplier,
                'name' => 'Mouse Gaming Logitech G502',
                'sku' => 'ELEC-MOU-001',
                'description' => 'Mouse gaming dengan sensor HERO 25K, 11 tombol programmable',
                'purchase_price' => 500000,
                'selling_price' => 650000,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $getCategoryId('Elektronik'),
                'supplier_id' => $defaultSupplier,
                'name' => 'Keyboard Mechanical Keychron K8',
                'sku' => 'ELEC-KEY-001',
                'description' => 'Keyboard mechanical wireless dengan switch Gateron',
                'purchase_price' => 800000,
                'selling_price' => 1100000,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $getCategoryId('Pakaian'),
                'supplier_id' => $suppliers->skip(1)->first()->id ?? $defaultSupplier,
                'name' => 'Kaos Polos Cotton Combed',
                'sku' => 'CLO-TSH-001',
                'description' => 'Kaos polos berbahan cotton combed 30s, nyaman dan adem',
                'purchase_price' => 35000,
                'selling_price' => 65000,
                'minimum_stock' => 50,
            ],
            [
                'category_id' => $getCategoryId('Pakaian'),
                'supplier_id' => $suppliers->skip(1)->first()->id ?? $defaultSupplier,
                'name' => 'Celana Jeans Slim Fit',
                'sku' => 'CLO-JEA-001',
                'description' => 'Celana jeans slim fit premium dengan bahan denim berkualitas',
                'purchase_price' => 120000,
                'selling_price' => 250000,
                'minimum_stock' => 30,
            ],
            [
                'category_id' => $getCategoryId('Makanan & Minuman'),
                'supplier_id' => $suppliers->skip(2)->first()->id ?? $defaultSupplier,
                'name' => 'Kopi Arabica Premium 250gr',
                'sku' => 'FNB-COF-001',
                'description' => 'Biji kopi arabica pilihan dari petani lokal',
                'purchase_price' => 45000,
                'selling_price' => 85000,
                'minimum_stock' => 20,
            ],
            [
                'category_id' => $getCategoryId('Makanan & Minuman'),
                'supplier_id' => $suppliers->skip(2)->first()->id ?? $defaultSupplier,
                'name' => 'Teh Hijau Organik 100gr',
                'sku' => 'FNB-TEA-001',
                'description' => 'Teh hijau organik tanpa pestisida',
                'purchase_price' => 25000,
                'selling_price' => 55000,
                'minimum_stock' => 25,
            ],
            [
                'category_id' => $getCategoryId('Peralatan Rumah Tangga'),
                'supplier_id' => $defaultSupplier,
                'name' => 'Panci Set Stainless Steel',
                'sku' => 'HOM-PAN-001',
                'description' => 'Set panci stainless steel 5 pieces dengan tutup kaca',
                'purchase_price' => 180000,
                'selling_price' => 350000,
                'minimum_stock' => 10,
            ],
            [
                'category_id' => $getCategoryId('Peralatan Rumah Tangga'),
                'supplier_id' => $defaultSupplier,
                'name' => 'Blender Miyako 2 Liter',
                'sku' => 'HOM-BLE-001',
                'description' => 'Blender dengan kapasitas 2 liter, pisau stainless steel',
                'purchase_price' => 200000,
                'selling_price' => 350000,
                'minimum_stock' => 8,
            ],
            [
                'category_id' => $getCategoryId('Olahraga'),
                'supplier_id' => $defaultSupplier,
                'name' => 'Matras Yoga Premium 6mm',
                'sku' => 'SPT-YOG-001',
                'description' => 'Matras yoga anti slip dengan ketebalan 6mm',
                'purchase_price' => 120000,
                'selling_price' => 225000,
                'minimum_stock' => 15,
            ],
            [
                'category_id' => $getCategoryId('Olahraga'),
                'supplier_id' => $defaultSupplier,
                'name' => 'Dumbbell Set 20kg',
                'sku' => 'SPT-DUM-001',
                'description' => 'Set dumbbell adjustable hingga 20kg',
                'purchase_price' => 450000,
                'selling_price' => 750000,
                'minimum_stock' => 5,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Products seeded successfully!');
    }
}
