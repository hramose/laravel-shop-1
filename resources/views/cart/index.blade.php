@extends('layouts.app')
@section('title', '購物車')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">我的購物車</div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>商品訊息</th>
        <th>單價</th>
        <th>數量</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody class="product_list">
      @foreach($cartItems as $item)
        <tr data-id="{{ $item->productSku->id }}">
          <td>
            <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
          </td>
          <td class="product_info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                <img src="{{ $item->productSku->product->image_url }}">
              </a>
            </div>
            <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
              <span class="product_title">
                <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">{{ $item->productSku->product->title }}</a>
              </span>
              <span class="sku_title">{{ $item->productSku->title }}</span>
              @if(!$item->productSku->product->on_sale)
                <span class="warning">該商品已下架</span>
              @endif
            </div>
          </td>
          <td><span class="price">￥{{ $item->productSku->price }}</span></td>
          <td>
            <input type="text" class="form-control form-control-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
          </td>
          <td>
            <button class="btn btn-sm btn-danger btn-remove">移除</button>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <div>
      <form class="form-horizontal" role="form" id="order-form">
        <div class="form-group row">
          <label class="col-form-label col-sm-3 text-md-right">選擇收貨地址</label>
          <div class="col-sm-9 col-md-7">
            <select class="form-control" name="address">
              @foreach($addresses as $address)
                <option value="{{ $address->id }}">{{ $address->full_address }} {{ $address->contact_name }} {{ $address->contact_phone }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-3 text-md-right">備註</label>
          <div class="col-sm-9 col-md-7">
            <textarea name="remark" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="offset-sm-3 col-sm-3">
            <button type="button" class="btn btn-primary btn-create-order">提交訂單</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
  $(document).ready(function () {
    // 監聽 移除 按鈕的點擊事件
    $('.btn-remove').click(function () {
      // $(this) 可以獲取到當前點擊的 移除 按鈕的 jQuery 物件
      // closest() 方法可以獲取到匹配選擇湇的第一個祖先元素，在這裡救是當前點擊的 移除 按鈕以上的 <tr> 標籤
      // data('id') 方法可以獲取到我們之前設置的 data-id 屬性的值，也就是對應的 SKU id
      var id = $(this).closest('tr').data('id');
      swal({
        title: "確認要將該商品移除",
        icon: "warning",
        buttons: ['取消', '確定'],
        dangerMode: true,
      })
      .then(function(willDelete) {
        // 使用者點擊 確定 按鈕，willDelete 的值就會是 true，否則為 false
        if (!willDelete) {
          return;
        }
        axios.delete('/cart/' + id)
          .then(function () {
            location.reload();
          })
      });
    });

    // 監聽 全選/取消全選 單選框的變更事件
    $('#select-all').change(function() {
      // 獲取單選框的選中狀態
      // prop() 方法可以知道邊謙中是否包含某個屬性，當單選框被勾選時，對應的標籤就會新增一個 checked 的屬性
      var checked = $(this).prop('checked');
      // 獲取所有 name=select 並且不帶有 disabled 屬性的勾選框
      // 對於已經下架的商品我們不希望對應的勾選框會被選中，因此我們需要加上 :not([disabled]) 這個條件
      $('input[name=select][type=checkbox]:not([disabled])').each(function() {
        // 將其勾選狀態設為與目標單選框一致
        $(this).prop('checked', checked);
      });
    });

    // 監聽建立訂單按鈕的點擊事件
    $('.btn-create-order').click(function () {
      // 構建請求參數，將使用者選擇的地址的 id 和背著內容寫入請求參數
      var req = {
        address_id: $('#order-form').find('select[name=address]').val(),
        items: [],
        remark: $('#order-form').find('textarea[name=remark]').val(),
      };
      // 遍歷 <table> 標籤內所有帶有 data-id 屬性的 <tr> 標籤，也就是每一個購物車中的商品 SKU
      $('table tr[data-id]').each(function () {
        // 獲取當前行的單選框
        var $checkbox = $(this).find('input[name=select][type=checkbox]');
        // 如果單選框被禁用或者沒有被選中則跳過
        if ($checkbox.prop('disabled') || !$checkbox.prop('checked')) {
          return;
        }
        // 獲取當前行中數量輸入框
        var $input = $(this).find('input[name=amount]');
        // 如果使用者將數量設為 0 或者不是一個數字，則也跳過
        if ($input.val() == 0 || isNaN($input.val())) {
          return;
        }
        // 把 SKU id 和數量存入請求參數陣列中
        req.items.push({
          sku_id: $(this).data('id'),
          amount: $input.val(),
        })
      });
      axios.post('{{ route('orders.store') }}', req)
        .then(function (response) {
          swal('訂單提交成功', '', 'success')
            .then(() => {
              location.href = '/orders/' + response.data.id;
            });
        }, function (error) {
          if (error.response.status === 422) {
            // http 狀態碼為 422 代表使用者輸入效驗失敗
            var html = '<div>';
            _.each(error.response.data.errors, function (errors) {
              _.each(errors, function (error) {
                html += error+'<br>';
              })
            });
            html += '</div>';
            swal({content: $(html)[0], icon: 'error'})
          } else {
            // 其他情況應該是系統掛了
            swal('系統錯誤', '', 'error');
          }
        });
    });


  });
</script>
@endsection
