<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class OrdersController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('訂單列表')
            ->body($this->grid());
    }

    protected function grid()
    {
        $grid = new Grid(new Order);

        // 只顯示已支付的訂單，並且默認按支付時間倒序排序
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        $grid->no('訂單流水號');
        // 展示關聯關係的欄位時，使用 column 方法
        $grid->column('user.name', '買家');
        $grid->total_amount('總金額')->sortable();
        $grid->paid_at('支付時間')->sortable();
        $grid->ship_status('物流')->display(function($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->refund_status('退款狀態')->display(function($value) {
            return Order::$refundStatusMap[$value];
        });
        // 禁用新增按鈕，後台不需要新增訂單
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用刪除和編輯按鈕
            $actions->disableDelete();
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
