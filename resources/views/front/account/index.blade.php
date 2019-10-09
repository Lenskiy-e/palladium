@extends('front.layouts.app')
@section('meta_title')  @endsection
@section('meta_description')  @endsection
@section('content')

    <button><a href="/profile/{{$profile->id}}/edit">Edit</a></button>

    <h1>{{$user->name}}</h1>
    <p><a href="mailto:{{$user->email}}">Mail</a></p>
    <p>{{$user->phone}}</p>

    <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>

    @include('front.account.orders', ['orders' => $orders])

    @include('front.layouts.extended_products', ['products' => $favorites])
@endsection
