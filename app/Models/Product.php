<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'description', 'image', 'on_sale',
        'rating', 'sold_count', 'review_count', 'price'
    ];

    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一個布林類型的欄位
    ];

    // 與商品SKU關聯
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function getImageUrlAttribute()
    {
        // 如果 image 本身就已經是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        // 這裡的\Storage::disk('public')的參數public需要和我們在config/admin.php裡面的upload.disk配置一致
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
