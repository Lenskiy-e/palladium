@extends('front.layouts.app')
@section('meta_title') {{$manufacturer->meta_title}} @endsection
@section('meta_description') {{$manufacturer->meta_description}} @endsection
@section('content')
@include('front.filters.filter-layouts')

<h1>{{$manufacturer->h1}}</h1>

@if ($manufacturer->image)
    <img width="200" src="/{{$manufacturer->image}}" alt="">
@endif

<p>{{$manufacturer->description}}</p>

@if ($categories->count())
    <form method="post" name="filter_form">
        @csrf
        <input type="hidden" name="id" value="{{$manufacturer->id}}">
        <input type="hidden" name="base_url" value="{{$manufacturer->url->url}}">
        <input type="hidden" name="get_params" value="{{$get_params}}">
        {!!$filter_categories!!}
    </form>
    <button data-redirect="" class="hide" id="filter-btn" data-text="Отфильтровать">Отфильтровать</button>
    <button data-redirect="/{{$manufacturer->url->url}}" id="filter-reset">@lang('filter.reset')</button>
@endif


@forelse ($categories as $category)
    <h2> <a href="/{{$category->url->url}}">{{$category->title}}</a> </h2>

    @include('front.layouts.extended_products', ['products' => $category->activeProducts()->paginate(env('PRODUCTS_PER_PAGE'))])

@empty
    @lang('manufacturer.empty')
@endforelse

{{ $categories->appends(Input::except('page'))->links() }}
@endsection
