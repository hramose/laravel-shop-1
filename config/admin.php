<?php

return [

    // 網站標題
    'name' => 'Laravel Shop',

    // 頁面頂部Logo
    'logo' => '<b>Laravel</b> Shop',

    // 頁面頂部小Logo
    'logo-mini' => '<b>La</b>',

    // 路由配置
    'route' => [
        // 路由前缀
        'prefix' => 'admin',
        // 控制器命名空間前缀
        'namespace' => 'App\\Admin\\Controllers',
        // 默認中間件列表
        'middleware' => ['web', 'admin'],
    ],

    // Laravel-Admin 的安裝目錄
    'directory' => app_path('Admin'),

    // Laravel-Admin 頁面標題
    'title' => 'Laravel Shop 管理後台',

    // 是否使用 https
    'https' => env('ADMIN_HTTPS', false),

    // Laravel-Admin 使用者認證設置
    'auth' => [

        'controller' => App\Admin\Controllers\AuthController::class,

        'guards' => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => Encore\Admin\Auth\Database\Administrator::class,
            ],
        ],
    ],

    // Laravel-Admin 文件上傳配置
    'upload' => [

        // 對應 filesystem.php 中的 disks
        'disk' => 'public',

        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],

    // Laravel-Admin 資料庫配置
    'database' => [

        // Database connection for following tables.
        // 資料庫連接名稱，留空即可
        'connection' => '',

        // User tables and model.
        // 管理員使用者資料表及模型
        'users_table' => 'admin_users',
        'users_model' => Encore\Admin\Auth\Database\Administrator::class,

        // Role table and model.
        // 角色資料表及模型
        'roles_table' => 'admin_roles',
        'roles_model' => Encore\Admin\Auth\Database\Role::class,

        // Permission table and model.
        // 權限表及模型
        'permissions_table' => 'admin_permissions',
        'permissions_model' => Encore\Admin\Auth\Database\Permission::class,

        // Menu table and model.
        // 選單及模型
        'menu_table' => 'admin_menu',
        'menu_model' => Encore\Admin\Auth\Database\Menu::class,

        // Pivot table for table above.
        // 多對多關聯中間表
        'operation_log_table'    => 'admin_operation_log',
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table'       => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table'        => 'admin_role_menu',
    ],

    // Laravel-Admin 操作日誌設置
    'operation_log' => [

        'enable' => true,

        // 不記操作日誌的路由
        'except' => [
            'admin/auth/logs*',
        ],
    ],

    // 地圖套件提供商
    'map_provider' => 'google',

    /*
    |--------------------------------------------------------------------------
    | Application Skin
    |--------------------------------------------------------------------------
    |
    | This value is the skin of admin pages.
    | @see https://adminlte.io/docs/2.4/layout
    |
    | Supported:
    |    "skin-blue", "skin-blue-light", "skin-yellow", "skin-yellow-light",
    |    "skin-green", "skin-green-light", "skin-purple", "skin-purple-light",
    |    "skin-red", "skin-red-light", "skin-black", "skin-black-light".
    |
    */
    // 頁面風格
    'skin' => 'skin-blue-light',

    /*
    |--------------------------------------------------------------------------
    | Application layout
    |--------------------------------------------------------------------------
    |
    | This value is the layout of admin pages.
    | @see https://adminlte.io/docs/2.4/layout
    |
    | Supported: "fixed", "layout-boxed", "layout-top-nav", "sidebar-collapse",
    | "sidebar-mini".
    |
    */
    'layout' => ['sidebar-mini', 'sidebar-collapse'],

    // 登入頁背景圖
    'login_background_image' => '',

    // 顯示版本
    'show_version' => true,

    // 顯示環境
    'show_environment' => true,

    // 選單綁定權限
    'menu_bind_permission' => true,

    // 啟用默認麵包屑
    'enable_default_breadcrumb' => true,

    // 擴展所在的目錄
    'extension_dir' => app_path('Admin/Extensions'),

    // 擴展設置
    'extensions' => [

    ],
];
