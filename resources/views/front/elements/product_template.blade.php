<div class="t-product">
    <div class="t-product-header">
    </div>
    <div class="t-product-image">
        <a href="#">
            <img src="{{URL::asset('uploads/products/39747_0.png')}}" alt="alt"
                 title="title">
        </a>
    </div>
    <div class="t-product-name">
        <a href="#">
            <span>Product name</span>
        </a>
    </div>
    <div class="t-product-price">
        @if(1 > 0)
            <p class="price-old"><span>150</span> <span
                        class="price-currency">грн</span></p>
        @endif
        <p class="price">170 <span class="price-currency">грн</span></p>

    </div>
    <span class="t-product-favorites">
            <svg role="img" class="favorites-icon">
        <use xlink:href="{{asset(env('SVG_ICONS_DIR'))}}#favorites-icon"></use>
    </svg>
    </span>
    <div class="t-product-footer">
        <button type="submit" class="button button-orange">Купить</button>
    </div>
</div>