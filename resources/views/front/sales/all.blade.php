@extends('front.layouts.app')

@section('content')
    @forelse($sales as $sale)
        <p>{{$sale->name}}</p>
        <p>{{$sale->description}}</p>
        <a href="{{url('sales/' . $sale->id)}}">
            <img src="{{URL::asset($sale->baner)}}">
        </a>
        <a href="{{url('sales/' . $sale->id)}}">Подробней</a>
    @empty
        Нету акций
    @endforelse
@stop
