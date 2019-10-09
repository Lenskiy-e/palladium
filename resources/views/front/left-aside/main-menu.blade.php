{{--<nav class="main-menu main-menu-home">--}}
{{--    <ul class="list-default catalog-items">--}}
{{--        <li class="list-item-default catalog-item">--}}
{{--            <a href="#">--}}
{{--                <div class="catalog-icon-svg">--}}
{{--                    <svg role="img" class="appliances-icon">--}}
{{--                        <use xlink:href="/svg/icons/svg-symbols.svg#appliances-icon"></use>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--                <div class="catalog-name"><span>Бытовая техника</span></div>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="list-item-default catalog-item">--}}
{{--            <a href="#">--}}
{{--                <div class="catalog-icon-svg">--}}
{{--                    <svg role="img" class="for-kids-icon">--}}
{{--                        <use xlink:href="/svg/icons/svg-symbols.svg#for-kids-icon"></use>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--                <div class="catalog-name"><span>Товары для детей</span></div>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--    </ul>--}}
{{--</nav>--}}

@if($categories)
    <nav class="main-menu main-menu-home">
        <ul class="list-default catalog-items">
            @foreach ($categories[1] as $key => $main_category)
                <li class="list-item-default catalog-item">
                    <a href="{{url($main_category['href'])}}">
                        <div class="catalog-icon-svg">
                            <svg role="img" class="{{$main_category['icon']}}">
                                <use xlink:href="/svg/icons/svg-symbols.svg#{{$main_category['icon']}}"></use>
                            </svg>
                        </div>
                        <div class="catalog-name"><span>{{$main_category['title']}}</span></div>
                    </a>

                    <div class="catalog-level-wrap">
                        @if(isset($categories[2]) &&  array_key_exists($key,$categories[2]))
                            <div class="catalog-level-items">
                                <div class="catalog-level-item">
                                    <div class="catalog-level-category">
                                        <ul>
                                            @foreach ($categories[2][$key] as $second_key => $second_category)
                                                <li>
                                                    <a href="{{url($second_category['href'])}}">
                                                        {{$second_category['title']}}
                                                    </a>
                                                </li>
                                                @if(isset($categories[3]) && array_key_exists($second_key,$categories[3]))
                                                    <ul>
                                                        @foreach ($categories[3][$second_key] as $third_key => $third_category)
                                                            <li>
                                                                <a href="{{url($third_category['href'])}}">
                                                                    {{$third_category['title']}}
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="catalog-top-brands">Топ Брендов</div>
                                </div>
                                <div class="catalog-action-banner">Баннер блок</div>
                            </div>
                        @endif

                    </div>
                </li>
            @endforeach
        </ul>
    </nav>
@endif
