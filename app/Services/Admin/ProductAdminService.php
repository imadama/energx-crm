<?php

namespace App\Services\Admin;

use App\Models\Product;

class ProductAdminService
{
    /**
     * @param array<string,mixed> $data
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    /**
     * @param array<int,array{id:int,order:int}> $items
     */
    public function updateOrderBulk(array $items): void
    {
        foreach ($items as $item) {
            Product::query()
                ->whereKey($item['id'])
                ->update(['order' => (int)$item['order']]);
        }
    }
}

