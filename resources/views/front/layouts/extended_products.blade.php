@forelse ($products as $product)
    <div class="category-product">
        @php
            $prices = $product->getPrice();
        @endphp
        <div class="t-product-id">
            <div class="product-id">
                <span>{{$product->id}}</span><br>
                <span class="product-id-text">@lang('product.text_product_id')</span>
            </div>
        </div>
        <div class="t-product-image">
            <a href="{{url($product->url->url)}}">
                <img src="{{URL::asset($product->product->image)}}" alt="{{$product->title}}"
                     title="{{$product->title}}">
            </a>
        </div>
        <div class="t-product-name">
            <a href="{{url($product->url->url)}}">
                <span>{{$product->name}}</span>
            </a>
        </div>
        <div class="t-product-wrap">
            <div class="t-product-price">
                @if($prices['old_price'])
                    <p class="price-old"><span>{{$prices['old_price']}}</span> <span
                            class="price-currency">грн</span></p>
                    @endif
                    <p class="price">{{$prices['price']}} <span class="price-currency">грн</span></p>
            </div>
            <div class="t-product-rating">
                <star-rating></star-rating>
                <a href="#">
                    <span class="rating-count">240</span>
                    <svg role="img" class="reviews-icon">
                        <use xlink:href="{{asset('svg/icons/svg-symbols.svg')}}#reviews-icon"></use>
                    </svg>
                </a>
            </div>
        </div>
        <div class="t-product-button">
            @if ($product->product->available === 3)
                @include('front.order.addButton', ['id' => $product->id, 'order_products' => $order_products])
                @endif
                @if(!SProduct::favorite($product->id))
                    @include('front.elements.button.add_favorite', ['id' => $product->id])
                @else
                    @include('front.elements.button.remove_favorite', ['id' => $product->id])
                @endif

        </div>
        <div class="product-data-short-description">
            {!!$product->short_description!!}
        </div>
    </div>
@empty
    @lang('category.empty_category')
@endforelse
