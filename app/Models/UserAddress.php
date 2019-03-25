<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    // 表示 last_used_at是一個時間日期類型，在之後的程式碼，$address->last_used_at返回的時一個時間日期物件
    // 確切來說是Carbon物件，Carbon是Laravel默認使用的時間日期處理類別
    protected $dates = ['last_used_at'];

    // 一個 User 可以有多個 UserAddress，一個 UserAddress 只能屬於一個 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 建立一個訪問器，之後程式碼可以直接通過 $address->full_address來獲取完整的地址，而不用每次去拼接
    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
