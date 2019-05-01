@extends('layouts.app')
@section('title', '查看訂單')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">
    <h4>訂單詳情</h4>
  </div>
  <div class="card-body">
    <table class="table">
      <thead>
      <tr>
        <th>商品資訊</th>
        <th class="text-center">單價</th>
        <th class="text-center">數量</th>
        <th class="text-right item-amount">小計</th>
      </tr>
      </thead>
      @foreach($order->items as $index => $item)
        <tr>
          <td class="product-info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                <img src="{{ $item->product->image_url }}">
              </a>
            </div>
            <div>
              <span class="product-title">
                 <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
              </span>
              <span class="sku-title">{{ $item->productSku->title }}</span>
            </div>
          </td>
          <td class="sku-price text-center vertical-middle">${{ $item->price }}</td>
          <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
          <td class="item-amount text-right vertical-middle">${{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
        </tr>
      @endforeach
      <tr><td colspan="4"></td></tr>
    </table>
    <div class="order-bottom">
      <div class="order-info">
        <div class="line"><div class="line-label">收貨地址：</div><div class="line-value">{{ join(' ', $order->address) }}</div></div>
        <div class="line"><div class="line-label">訂單備註：</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
        <div class="line"><div class="line-label">訂單編號：</div><div class="line-value">{{ $order->no }}</div></div>
      </div>
      <div class="order-summary text-right">
        <div class="total-amount">
          <span>訂單總價：</span>
          <div class="value">${{ $order->total_amount }}</div>
        </div>
        <div>
          <span>訂單狀態：</span>
          <div class="value">
            @if($order->paid_at)
              @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                已支付
              @else
                {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
              @endif
            @elseif($order->closed)
              已關閉
            @else
              未支付
            @endif
          </div>
        </div>
        <!-- 支付按鈕開始 -->
        @if(!$order->paid_at && !$order->closed)
        <div class="payment-buttons">
          <a class="btn btn-primary btn-sm" href="{{ route('payment.alipay', ['order' => $order->id]) }}">支付寶支付</a>
          <button class="btn btn-sm btn-success" id='btn-wechat'>微信支付</button>
        </div>
        @endif
        <!-- 支付按鈕結束 -->
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
  $(document).ready(function() {
    // 微信支付按鈕事件
    $('#btn-wechat').click(function() {
      swal({
        // content 參數可以是一個DOM元素，這裡我們用jQuery動態產生一個img標籤，並通過[0]的方式取得DOM元素
        content: $('<img src="{{ route('payment.wechat', ['order' => $order->id]) }}" />')[0],
        // buttons參數可以設置按鈕顯示
        buttons: ['關閉', '已完成付款'],
      })
      .then(function(result) {
      // 如果使用者點擊了 已完成付款 按鈕，則重新加載頁面
        if (result) {
          location.reload();
        }
      })
    });
  });
</script>
@endsection
