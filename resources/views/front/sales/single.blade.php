@extends('front.layouts.app')

@section('content')
    <h1>{{$sale->name}}</h1>

    <p>{{$sale->description}}</p>

    <img src="{{URL::asset($sale->baner)}}">

@stop
