<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use App\Events\OrderPaid;

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

    public function payByWechat(Order $order, Request $request) {
        // 校驗權限
        $this->authorize('own', $order);
        // 校驗訂單狀態
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('訂單狀態不正確');
        }

        // scan 方法為拉起微信掃碼支付
        $wechatOrder = app('wechat_pay')->scan([
            'out_trade_no' => $order->no,  // 商品訂單流水號，與支付寶 out_trade_no 一樣
            'total_fee' => $order->total_amount * 100, // 與支付寶不同，微信支付的金額單位是分
            'body'      => '支付 Laravel Shop 的訂單：'.$order->no, // 訂單描述
        ]);

        // 把要轉換的字串作為QRCode的構造函數參數
        $qrCode = new QrCode($wechatOrder->code_url);

        // 將產生的二維條碼圖片數據以字串形式輸出，並加上對應的回應類型
        return response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
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

        $this->afterPaid($order);
        return app('alipay')->success();
    }

    // 微信只有後端回調
    public function wechatNotify()
    {
        // 校驗回調參數是否正確
        $data = app('wechat_pay')->verify();
        // 找到對應的訂單
        $order = Order::where('no', $data->out_trade_no)->first();
        // 訂單不存在則告知微信支付
        if (!$order) {
            return 'fail';
        }
        // 訂單已支付
        if ($order->paid_at) {
            // 告知微信支付此訂單已處理
            return app('wechat_pay')->success();
        }

        // 將訂單標記為已支付
        $order->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => 'wechat',
            'payment_no'     => $data->transaction_id,
        ]);

        $this->afterPaid($order);
        return app('wechat_pay')->success();
    }

    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }
}
