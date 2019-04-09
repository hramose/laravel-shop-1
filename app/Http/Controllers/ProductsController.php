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
}
