@extends('front.layouts.app')
@section('meta_title') Заказ @endsection
@section('meta_description') ЗАКАЗ @endsection

@section('content')
    <form id="order_data" method="post" action="{{url('order')}}/{{$order->id}}">
        @csrf
        {{method_field('PUT')}}
        <input type="hidden" name="order_id" value="{{$order->id}}">

        <div class="order_user">
            @include('front.order.user_exists')

            @if (Auth()->user())
                @include('front.order.user', ['user' => $user])
            @else
                @include('front.order.guest', ['user' => $user])
            @endif
        </div>

        <div class="promocode">
            <input type="text" name="promocode" value="@if($order->promocode) {{$order->promocode->code}} @endif" placeholder="введите промо-код">
            <strong>{{ $errors->first('promocode') }}</strong>
            <button id="enter_promo">
                применить
            </button>
            <span class="error" id="promo-error"></span>
        </div>

        <input type="submit" value="Submit">
    </form>

        <div class="order_products">
            @include('front.order.products', ['products' => $products])
        </div>

        <div class="order_totals">
            @include('front.order.totals', ['order' => $order])
        </div>
@endsection
