<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        // 建立一個查詢建構器
        $builder = Product::query()->where('on_sale', true);
        // 判斷是否有提交 search 參數，如果有就赋值給 $search 變數
        // search 參數用來模糊搜尋商品
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // 模糊搜尋商品標題、商品詳情、SKU 標題、SKU描述
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        // 是否有提交 order 參數，如果有就赋值給 $order 變數
        // order 參數用來控制商品的排序規則
        if ($order = $request->input('order', '')) {
            // 是否是以 _asc 或者 _desc 結尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字串的開頭是這3個字串之一，說明是一個合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // 根據傳入的排序值來建立排序參數
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate(16);
        return view('products.index', [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }

    public function show(Product $product, Request $request)
    {
        // 判斷商品是否已經上架，如果沒有上架則拋出異常
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        // 使用者未登入時返回的是 null，已登入時返回的是對應的使用者物件
        if($user = $request->user()) {
            // 從目前使用者已收藏的商品中搜尋 id 為當前商品 id 的商品
            // boolval() 函數用於把值轉為布林值
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        return view('products.show', ['product' => $product, 'favored' => $favored]);
    }

    // 收藏商品
    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        // 先判斷目前使用者是否已經收藏商品，如果已經收藏則不做任何操作返回
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }
        // 否則通過 attach()方法將目前使用者和此商品關聯起來
        //attach()方法的參數可以是模型的id，也可以是模型物件本身
        //這裡也可以寫成attach($product->id)
        $user->favoriteProducts()->attach($product);

        return [];
    }

    // 取消收藏商品
    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        // detach()方法用於取消多對多關聯，接受參數與 attach() 方法一致
        $user->favoriteProducts()->detach($product);

        return [];
    }
}
