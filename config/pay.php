<?php

return [
    'alipay' => [
        'app_id'         => '在支付寶沙箱看到的appid',
        'ali_public_key' => '支付寶沙箱顯示的公鑰',
        'private_key'    => '產生的私鑰',
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
