<form id="order_products">
    @forelse ($products as $product)
        <div class="order_product">
            <input type="hidden" name="order_product_id" value="{{$product->id}}">
            <b class="order_delete">X</b>
            <img style="width: 30px;" src="/{{$product->product->image}}" alt="No photo">
            <span>{{$product->name}}</span>
            <span>{{$product->pivot->count}} * </span>
            <span>{{$product->getPrice()['price']}} грн</span>
        </div>
    @empty
        <h4>корзина пуста :(</h4>
    @endforelse
</form>

