@forelse ($orders as $order)
    <div class="order-list">
        <div>{{$order->created_at->format('d.m.Y')}}</div>
        <div>№{{$order->id}}</div>
        <div>{{$order->total_amount}} грн</div>
        <div>{{$order->total_count}} шт</div>
        <div>@lang('order.order-status-' . $order->complete)</div>

        <div class="order_products">
            @foreach ($order->product as $product)
                <img width="100" src="{{URL::asset($product->product->image)}}" alt="No image">
                <a href="{{url($product->url->url)}}">{{$product->name}}</a>
                {{--Картинка под статус--}}
                <a href="{{url($product->url->url . '#reviews')}}">Оставить отзыв</a>
            @endforeach
        </div>
    </div>
@empty
    Нету заказов
@endforelse
