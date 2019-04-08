<?php

use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // 創建 30 個商品
        $products = factory(\App\Models\Product::class, 30)->create();
        foreach ($products as $product) {
            // 建立 3 個 SKU，並且每個 SKU 的 `product_id` 欄位都設為當前循環的商品 id
            $skus = factory(\App\Models\ProductSku::class, 3)->create(['product_id' => $product->id]);
            // 找出價格最低的 SKU 價格，把商品價格設置為該價格
            $product->update(['price' => $skus->min('price')]);
        }
    }
}
