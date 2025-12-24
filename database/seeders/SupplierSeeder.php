<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'PT Elektronik Indonesia',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'phone' => '021-1234567',
                'email' => 'elektronik@supplier.com',
            ],
            [
                'name' => 'CV Tekstil Jaya',
                'address' => 'Jl. Asia Afrika No. 45, Bandung',
                'phone' => '022-7654321',
                'email' => 'tekstil@supplier.com',
            ],
            [
                'name' => 'UD Makanan Sejahtera',
                'address' => 'Jl. Gatot Subroto No. 78, Surabaya',
                'phone' => '031-9876543',
                'email' => 'makanan@supplier.com',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
