<header id="header">
    <div class="wrapper-full">
        <div class="top-menu">
            <div id="for-customer">
                <div class="for-customer">
                    <svg role="img" class="information-icon svg-icon">
                        <use xlink:href="{{URL::asset('svg/icons/svg-symbols.svg#information-icon')}}"></use>
                    </svg>
                    <span class="drop-arrow">Для клиента</span>
                </div>
                <nav>
                    <ul class="for-customer-items drop-default">
                        @forelse($header_links as $link)
                            <li class="for-customer-item">
                                <a href="/{{$link['slug']}}" class="@activeClass($link['slug'])">
                                    {{$link['name']}}
                                </a>
                            </li>
                        @empty

                        @endforelse
                    </ul>
                </nav>
            </div>
            <div id="personal-area">
                <div class="personal-area">
                    <svg role="img" class="user-icon svg-icon">
                        <use xlink:href="{{URL::asset('svg/icons/svg-symbols.svg#user-icon')}}"></use>
                    </svg>
                    <span id="personal-area-btn" class="drop-arrow">@lang("header.text_account")</span>
                </div>

                <div class="account drop-default">
                    @if (Auth()->user())
                        <a href="#" rel="nofollow" id="logout-btn">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            {{ csrf_field() }}
                        </form>
                        @if (!Auth()->user()->is_admin)
                            <a href="/profile">@lang('header.text_profile')</a>
                        @endif
                    @else
                        <a href="#" rel="nofollow" class="modal-open" data-modal="register">@lang('header.text_registration')</a>
                        <a href="#" rel="nofollow" class="modal-open" data-modal="login">@lang('header.text_login')</a>
                    @endif
                </div>
            </div>
            <ul id="lang-switch" class="lang-items">
                @foreach (SLocale::getLocales() as $locale)
                    <li class="lang-item @if(app()->getLocale() === $locale['key'])active @endif"><a href="{{$locale['href']}}">{{$locale['title']}}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="wrapper-full">
        <div class="header">
            <div id="logo">
                @if(url()->current() === url('/'))
                    <img src="{{$common_data->logo}}" alt="{{$common_data->name}}"
                         title="{{$common_data->name}}">
                @else
                    <a href="/">
                        <img src="{{$common_data->logo}}" alt="{{$common_data->name}}"
                             title="{{$common_data->name}}">
                    </a>
                @endif
            </div>
            <div class="search-wrapper">
                <svg role="img" class="search-icon">
                    <use xlink:href="{{URL::asset('svg/icons/svg-symbols.svg#search-icon')}}"></use>
                </svg>
                <input type="text" id="search-input" class="input-default" autocomplete="off"
                       placeholder="@lang("header.text_search_placeholder")" name="search">
                <button id="search-btn" class="button button-blue">
                    <span>@lang("header.text_search")</span>
                    <svg role="img" class="search-icon">
                        <use xlink:href="{{URL::asset('svg/icons/svg-symbols.svg#search-icon')}}"></use>
                    </svg>
                </button>
            </div>
            <div id="compare">
                <a href="#" rel="nofollow">
                    <svg role="img" class="compare-icon">
                        <use xlink:href="{{URL::asset('svg/icons/svg-symbols.svg#compare-icon')}}"></use>
                    </svg>
                    <span>@lang("header.text_compare")</span></a></div>
            <div id="favorites">
                <a href="#" rel="nofollow">
                    <svg role="img" class="favorites-fill-icon">
                        <use xlink:href="{{URL::asset('svg/icons/svg-symbols.svg#favorites-fill-icon')}}"></use>
                    </svg>
                    <span>@lang("header.text_favorites")</span></a></div>
            <div id="cart">
                <a href="/order">
                    <svg role="img" class="cart-icon">
                        <use xlink:href="{{URL::asset('svg/icons/svg-symbols.svg#cart-icon')}}"></use>
                    </svg>
                    <span>@lang("header.text_cart")</span></a>
            </div>
        </div>

    </div>
    <div class="wrapper-full">
        <div class="header-bottom">
            <div id="catalog" class="menu-catalog"><span>@lang("header.text_catalog")</span>
                <svg role="img" class="category-icon">
                    <use xlink:href="{{URL::asset('svg/icons/svg-symbols.svg#category-icon')}}"></use>
                </svg>
            </div>
            <div class="header-bottom-link">
                <div class="link-items">
                    <div class="link-item"><a class="active" href="/top-brendov"><span>@lang('header.text_top_brands')</span></a></div>
                    <div class="link-item"><a href="/products_new"><span>@lang('header.text_new_product')</span></a></div>
                    <div class="link-item"><a href="{{url('sales')}}"><span>@lang('header.text_action')</span></a></div>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
</header>
