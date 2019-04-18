<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class OrderRequest extends Request
{
    public function rules()
    {
        return [
            // 判斷使用者送出的地址 ID 是否存在於資料庫並且屬於當前使用者
            // 後面這個條件非常重要，否則惡意使用者可以用不同的地址 ID 不斷提交訂單來遍歷出平台所有使用者的收貨地址
            'address_id'     => [
                'required',
                Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id),
            ],
            'items'  => ['required', 'array'],
            'items.*.sku_id' => [ // 檢查 items 陣列下每一個子陣列的 sku_id 參數
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('該商品不存在');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('該商品未上架');
                    }
                    if ($sku->stock === 0) {
                        return $fail('該商品已售完');
                    }
                    // 獲取當前索引
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index = $m[1];
                    // 根據索引找到使用者所提交的購買數量
                    $amount = $this->input('items')[$index]['amount']; //$this->input('item')[0]['amount'] 就是使用者想要購買的數量
                    if ($amount > 0 && $amount > $sku->stock) {
                        return $fail('該商品庫存不足');
                    }
                },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
