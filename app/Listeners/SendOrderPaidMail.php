<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// implements ShouldQueue 代表異步監聽器
class SendOrderPaidMail implements ShouldQueue
{
    public function handle(OrderPaid $event)
    {
        // 從事件物件中取出對應的訂單
        $order = $event->getOrder();
        // 運用 notify 方法來發送通知
        $order->user->notify(new OrderPaidNotification($order));
    }
}
