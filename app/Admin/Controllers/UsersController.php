<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
{
    use HasResourceActions;

    // 用來顯示使用者列表，通過調用grid()方法來決定要展示哪些欄位，以及各個欄位對應的名稱，
    // Laravel-Admin 會通過讀取資料庫自動把所有的欄位都列出來
    public function index(Content $content)
    {
        return $content
            ->header('使用者列表')
            ->description('description')
            ->body($this->grid());
    }


    protected function grid()
    {
        $grid = new Grid(new User);

        // 內容是使用者的 ID 欄位
        $grid->id('Id');

        // 內容是使用者的 name 欄位，下面的 email() 和 created_at() 同理
        $grid->name('使用者名稱');
        $grid->email('電子郵件');

        // display()方法接受一個匿名函數作為參數，在顯示時會把對應欄位值當成參數傳給匿名函數，
        // 把匿名函數的返回值作為頁面輸出的內容
        // 在這個例子就是當 email_verified_at 有值時顯示「是」，及驗證過墊子信箱，否則顯示「否」
        $grid->email_verified_at('已驗證電子郵件')->display(function ($value) {
            return $value ? '是' : '否';
        });

        $grid->created_at('註冊時間');

        // 不在頁面顯示`新建`按鈕，因為我們不需要在後台新建使用者
        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            // 不在每一行後面顯示查看按鈕
            $actions->disableView();
            // 不在每一行後面顯示刪除按鈕
            $actions->disableDelete();
            // 不在每一行後面顯示編輯按鈕
            $actions->disableEdit();
        });

        $grid->tools(function ($tools) {
            // 禁用批量刪除按鈕
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }
}
