@extends('front.layouts.app')
@push('owl')
    <link rel="stylesheet" href="{{asset('libs/owl.carousel/owl.carousel.min.css')}}">
    <script src="{{asset('libs/owl.carousel/owl.carousel.min.js')}}"></script>
@endpush
@section('meta_title'){{$common_data->meta_title}}@endsection
@section('meta_description'){{$common_data->meta_description}}@endsection
@section('content')
    <div class="home-page">
        <div class="wrapper-full">
            <div class="page-row">
                <aside class="left-aside">
                    @include('front.left-aside.main-menu')
                    @include('front.left-aside.left-aside-info')
                </aside>
                <div class="slide-block">
                    @include('front.layouts.slideShow')
                </div>
            </div>
        </div>
        <div class="wrapper-full">
            <h2>Новинки</h2>
            <div class="page-row">
                @include('front.elements.product_template')
                @include('front.elements.product_template')
                @include('front.elements.product_template')
                @include('front.elements.product_template')
                @include('front.elements.product_template')
                @include('front.elements.product_template')
                @include('front.elements.product_template')
                @include('front.elements.product_template')
            </div>
        </div>
    </div>
@endsection