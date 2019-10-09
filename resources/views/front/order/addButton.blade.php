<a @if (!in_array($id, $order_products)) style="display: none" @endif id="in_order_btn_{{$id}}"
   href="/order">@lang('product.text_go_cart')</a>
<button @if (in_array($id, $order_products)) style="display: none" @endif class="addCart button button-orange" id="out_order_btn_{{$id}}"
        data-id="{{$id}}">
    <svg role="img" class="cart-icon">
        <use xlink:href="{{asset('svg/icons/svg-symbols.svg')}}#cart-icon"></use>
    </svg>
    <span>@lang('product.text_add_cart')</span>
</button>

