<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Product;

class SufficientStock implements Rule
{
    protected $productId;
    protected $type;
    protected $availableStock;

    /**
     * Create a new rule instance.
     *
     * @param  int  $productId
     * @param  string  $type
     * @return void
     */
    public function __construct($productId, $type)
    {
        $this->productId = $productId;
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Hanya validasi untuk transaksi keluar
        if ($this->type !== 'keluar') {
            return true;
        }

        $product = Product::find($this->productId);
        
        if (!$product) {
            return false;
        }

        $this->availableStock = $product->current_stock;
        
        // Cek apakah quantity yang diminta tidak melebihi stok tersedia
        return $this->availableStock >= $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Stok tidak mencukupi! Stok tersedia: {$this->availableStock} unit.";
    }
}
