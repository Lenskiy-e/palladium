@extends('front.layouts.app')
@section('meta_title') {{$category->meta_title}} @endsection
@section('meta_description') {{$category->meta_description}} @endsection
@section('content')
    {!! SBreadcrumbs::get('category', $category->id) !!}
    <div class="wrapper-full">
        <h1>{{$category->h1}}</h1>
        @if ($category->category->image)
            <p><img width="500" src="{{URL::asset($category->category->image)}}" alt="no image"></p>
        @endif

        @foreach ($category->children as $child)
            <a href="{{url($child->url->url)}}"><img width="200" src="{{URL::asset($child->category->image)}}" alt=""></a>
            <a href="{{url($child->url->url)}}"><p>{{$child->title}}</p></a>
        @endforeach
    </div>
@endsection
