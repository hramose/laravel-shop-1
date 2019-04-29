<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        // 判斷訂單是否屬於當前使用者
        $this->authorize('own', $order);
        // 訂單已支付或者已關閉
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('訂單狀態不正確');
        }

        // 調用支付寶的網頁支付
        return app('alipay')->web([
            'out_trade_no' => $order->no, // 訂單編號，須保證在商品端不重複
            'total_amount' => $order->total_amount, // 訂單金額，單位元，支持小數點後兩位
            'subject'      => '支付 Laravel Shop 的訂單'.$order->no, // 訂單標題
        ]);
    }

    // 前端回調頁面
    public function alipayReturn()
    {
        try {
            // 效驗提交的參數是否合法
            // $data = app('alipay')->verify();
            // dd($data);
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '數據不正確']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    // 後端回調
    public function alipayNotify()
    {
        // $data = app('alipay')->verify();
        // \Log::debug('Alipay notify', $data->all());

        // 校驗輸入參數
        $data  = app('alipay')->verify();
        // 如果訂單狀態不是成功或是結束，則不走後續的處理
        // 所有交易狀態：https://docs.open.alipay.com/59/103672
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到訂單流水號，並在資料庫中查詢
        $order = Order::where('no', $data->out_trade_no)->first();
        // 不太可能出現支付一筆不存在的訂單，這個判斷指示加強系統完整性
        if (!$order) {
            return 'fail';
        }
        // 如果這筆訂單的狀態已經是支付
        if ($order->paid_at) {
            // 返回數據給支付寶
            return app('alipay')->success();
        }

        $order->update([
            'paid_at' => Carbon::now(), // 支付時間
            'payment_method' => 'alipay', // 支付方式
            'payment_no' => $data->trade_no, // 支付寶訂單號
        ]);

        return app('alipay')->success();
    }
}
