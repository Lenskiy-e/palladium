<div id="right-menu">
    <div class="r-menu-wrap">
        <ul class="r-menu">
            <li class="r-menu-item scroll-top"></li>
            <li class="r-menu-item"><a href="#">@lang('header.text_cart')</a>
                <span class="r-menu-icon">
                    <svg role="img" class="cart-icon">
                        <use xlink:href="{{asset(env('SVG_ICONS_DIR'))}}#cart-icon"></use>
                    </svg>
                </span>
            </li>
            <li class="r-menu-item"><a href="#">@lang('header.text_favorites')</a>
                <span class="r-menu-icon">
                    <svg role="img" class="favarites-icon">
                        <use xlink:href="{{asset(env('SVG_ICONS_DIR'))}}#favarites-icon"></use>
                    </svg>
                </span>
            </li>
            <li class="r-menu-item"><a href="#">@lang('header.text_compare')</a>
                <span class="r-menu-icon">
                    <svg role="img" class="compare-icon">
                        <use xlink:href="{{asset(env('SVG_ICONS_DIR'))}}#compare-icon"></use>
                    </svg>
                </span>
            </li>
            <li class="r-menu-item"><a href="/akcii">@lang('header.text_action')</a>
                <span class="r-menu-icon">
                    <svg role="img" class="promotions-icon">
                        <use xlink:href="{{asset(env('SVG_ICONS_DIR'))}}#promotions-icon"></use>
                    </svg>
                </span>
            </li>
        </ul>
    </div>
</div>

{{--todo change path for svg icon {{asset(env('SVG_ICONS_DIR'))}} --}}