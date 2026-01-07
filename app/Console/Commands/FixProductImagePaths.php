<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class FixProductImagePaths extends Command
{
    protected $signature = 'products:fix-image-paths';
    protected $description = 'Fix product image paths that are missing products/ prefix';

    public function handle()
    {
        $this->info('Checking for products with incorrect image paths...');

        $products = Product::whereNotNull('image')
            ->where('image', '!=', '')
            ->get();

        $this->info("Total products with images: {$products->count()}");

        $fixed = 0;
        foreach ($products as $product) {
            // Check if image path doesn't start with 'products/'
            if (strpos($product->image, 'products/') === false) {
                $this->warn("Found: ID {$product->id} - {$product->name}");
                $this->warn("  Old path: {$product->image}");

                // Update to correct path
                $product->image = 'products/' . $product->image;
                $product->save();

                $this->info("  New path: {$product->image}");
                $fixed++;
            }
        }

        $this->info("\nFixed {$fixed} product image paths.");
        return 0;
    }
}
