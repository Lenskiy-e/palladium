<div class="products">
    <form id="order_products">
        @csrf
        @forelse ($products as $product)
        <div class="cart_product">
            <input type="hidden" name="order_product_id" value="{{$product->id}}">
            <b class="cart_delete">X</b>
            <img style="width: 30px;" src="/{{$product->product->image}}" alt="No photo">
            <span>{{$product->name}}</span>
            <span class="cart_minus">-</span>
            <input type="text" pattern="[0-9]+" class="input-default cart_count" name="cart_count[{{$product->id}}]" value="{{$product->pivot->count}}">
            <span class="cart_plus">+</span>
            <span>{{$product->getPrice()['price']}} грн</span>
        </div>
        @empty
            <h4>корзина пуста :(</h4>
        @endforelse
    </form>
</div>
