@extends('front.layouts.app')
@section('meta_title')  @endsection
@section('meta_description')  @endsection
@section('content')

<form action="/profile/{{$user->id}}" method="post">
    {{method_field('PUT')}}
    @csrf
    @include('front.account.base')
    @include('front.account.additional')
    @include('front.account.childrens')
    @include('front.account.hobbies')
    <input type="submit" value="Подтвердить">
</form>

@endsection
