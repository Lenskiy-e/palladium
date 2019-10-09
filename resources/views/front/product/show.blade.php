@php
    $stock_status = ['archival','not_in_stock','waiting','in_stock'];
    $price = [$product->product->rrc_price,$product->product->base_price];
    $locale = App::getLocale();
@endphp
@extends('front.layouts.app')
@push('field_style')
    <script src="js1.js"></script>
@endpush
@section('meta_title') {{$product->meta_title}} @endsection
@section('meta_description') {{$product->meta_description}} @endsection
@section('content')
<div class="wrapper-full">
{{--    <div class="breadcrumbs">--}}
{{--        @foreach ($product->breadcrumbs() as $bread)--}}
{{--            <a href="{{$bread['href']}}">{{$bread['name']}}</a>--}}
{{--            <b> |</b>--}}
{{--        @endforeach--}}
{{--        <span>{{$product->title}}</span>--}}
{{--    </div>--}}

    {!! SBreadcrumbs::get('product', $product->id) !!}

    @if ($product->edit_status !== 3)
        <div class="error">
            @lang('product.edit_alert')
        </div>
    @endif
    <h1>{{$product->name}}</h1>

    <h3>{{$product->manufacturer->title}}</h3>

    <p>{{$product->name}}</p>
    {!!$product->short_description!!}
    {!!$product->description!!}

    <p><img src="{{URL::asset($product->product->image)}}" alt="No photo"></p>

    @if ($product->photos)
        @foreach (square_brackets_to_dots($product->photos) as $photo)
            <img src="{{URL::asset($photo)}}" alt="бульк" width="200">
        @endforeach
    @endif

    @if ($product->product->markdown && $product->markdown_reason)
        <i>@lang("product.markdown_reason")</i> : <b>{{$product->markdown_reason}}</b>
    @endif

    @if (!$product->product->adult)
        <b>18+</b>
    @endif

    <p>@lang("product.status"): @lang("product.{$stock_status[$product->product->available]}")</p>
    {{--resources/lang--}}
    @if (!$product->similar->isEmpty())
        <h4>@lang("product.similar")</h4>

        @foreach ($product->similar as $similar)
        @php
            $similar_param = $similar->parameters->where('attribute_id',$similar->pivot->attribute_id)->first();
        @endphp
            <a href="{{url($similar->url->url)}}">
                @if($similar_param && $similar_param->image)
                    <img width="50" height="50"
                         src="{{url($similar_param->image)}}"
                         alt="{{$similar->title}}">
                @else
                    {{$similar->title}}
                @endif
            </a>
        @endforeach
    @endif

    <h4>@lang("product.price"): </h4>
    @if ($product->product->sale_price)
        <s>{{$price[$product->product->price_mark]}}</s>
        <p>{{$product->product->sale_price}}</p>
    @else
        <p>{{$price[$product->product->price_mark]}}</p>
    @endif

    <table>
        <thead>
        <tr>
            <th>@lang("product.attribute")</th>
            <th>@lang("product.parameter")</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($product->parameters as $param)
            <tr>
                <td>{{$param->attribute->title}}</td>
                <td>{{json_decode($param->title)->$locale}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if ($product->product->available === 3)
        @include('front.order.addButton', ['id' => $product->id])
    @endif
    @include('front.elements.button.add_favorite', ['id' => $product->id])
</div>

@endsection
