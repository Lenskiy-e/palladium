@extends('front.layouts.app')
@section('meta_title') {{$category->meta_title}} @endsection
@section('meta_description') {{$category->meta_description}} @endsection
@section('content')
    {!! SBreadcrumbs::get('category', $category->id) !!}
    <div class="wrapper-full">
        <h1>{{$category->h1}}</h1>
        <div class="page-row">
            <div class="left-aside">
                @if($products->count())
                    <div class="filter-category-product">
                        @include('front.filters.filter-layouts')
                        <form method="post" name="filter_form">
                            @csrf
                            <input type="hidden" name="id" value="{{$category->id}}">
                            <input type="hidden" name="base_url" value="{{$category->url->url}}">
                            <input type="hidden" name="get_params" value="{{$get_params}}">
                            {!!$price_filter!!}
                            {!!$filter!!}
                        </form>
                        <div class="button-wrap">
                            <button data-redirect="/{{$category->url->url}}" id="filter-reset"
                                    class="button button-attention">
                                @lang('product.text_filter_clear')
                            </button>
                            <button data-redirect="" style="display:none;" id="filter-btn"
                                    data-text="@lang('product.text_filter_success')"
                                    class="button button-success"></button>
                        </div>
                    </div>
                @endif
            </div>

            <main class="category-products">
                <div class="page-row">
                    @include('front.layouts.extended_products', ['data' => $products])
                    {{ $products->appends(Input::except('page'))->links() }}
                </div>

            </main>
        </div>
    </div>

    <script>
        Vue.component('star-rating', {

            props: {
                'name': String,
                'value': null,
                'id': String,
                'disabled': Boolean,
                'required': Boolean
            },

            template: '<div class="star-rating">\
			<label class="star-rating__star" v-for="rating in ratings" \
			:class="{\'is-selected\': ((value >= rating) && value != null), \'is-disabled\': disabled}" \
			v-on:click="set(rating)" v-on:mouseover="star_over(rating)" v-on:mouseout="star_out">\
			<input class="star-rating star-rating__checkbox" type="radio" :value="rating" :name="name" \
			v-model="value" :disabled="disabled">★</label></div>',

            /*
             * Initial state of the component's data.
             */
            data: function () {
                return {
                    temp_value: null,
                    ratings: [1, 2, 3, 4, 5]
                };
            },

            methods: {
                /*
                 * Behaviour of the stars on mouseover.
                 */
                star_over: function (index) {
                    var self = this;

                    if (!this.disabled) {
                        this.temp_value = this.value;
                        return this.value = index;
                    }

                },

                /*
                 * Behaviour of the stars on mouseout.
                 */
                star_out: function () {
                    var self = this;

                    if (!this.disabled) {
                        return this.value = this.temp_value;
                    }
                },

                /*
                 * Set the rating.
                 */
                set: function (value) {
                    var self = this;

                    if (!this.disabled) {
                        this.temp_value = value;
                        return this.value = value;
                    }
                }
            }
        });

        new Vue({
            el: '.t-product-rating'
        });
    </script>

@endsection
