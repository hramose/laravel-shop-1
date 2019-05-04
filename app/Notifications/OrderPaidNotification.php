<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class OrderPaidNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // 我們只需要通過電子信箱通知，因此這裡只需要一個 mail 即可
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('訂單支付成功')  // 郵件標題
            ->greeting($this->order->user->name.'您好：') // 歡迎的話
            ->line('您在 '.$this->order->created_at->format('m-d H:i').' 建立的訂單已經支付成功') // 郵件內容
            ->action('查看訂單', route('orders.show', [$this->order->id])) // 郵件中的按鈕及對應連結
            ->success(); // 按鈕的色調
    }
}
