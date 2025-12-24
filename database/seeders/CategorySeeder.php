<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik dan gadget'],
            ['name' => 'Pakaian', 'description' => 'Pakaian pria dan wanita'],
            ['name' => 'Makanan & Minuman', 'description' => 'Produk makanan dan minuman'],
            ['name' => 'Peralatan Rumah Tangga', 'description' => 'Peralatan untuk keperluan rumah tangga'],
            ['name' => 'Olahraga', 'description' => 'Peralatan dan perlengkapan olahraga'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
