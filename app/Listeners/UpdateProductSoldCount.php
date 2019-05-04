<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OrderItem;

// implements ShouldQueue 代表此監聽器是異步執行的
class UpdateProductSoldCount implements ShouldQueue
{
    // Laravel 會默認執行監聽器的 handle 方法，觸發的事件會作為 handle 方法的參數
    public function handle(OrderPaid $event)
    {
        // 從事件物件中取出對應的訂單
        $order = $event->getOrder();
        // 預加載商品資料
        $order->load('items.product');
        // 循環遍歷訂單的商品
        foreach ($order->items as $item) {
            $product   = $item->product;
            // 計算對應商品的銷量
            $soldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');  // 關聯的訂單狀態是已支付
                })->sum('amount');
            // 更新商品銷量
            $product->update([
                'sold_count' => $soldCount,
            ]);
        }
    }
}
