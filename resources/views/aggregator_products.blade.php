@extends('backpack::layout')

@section('header')
@endsection

@section('content')
    <table class="box table table-striped table-hover display responsive nowrap m-t-0" cellspacing="0">
        <thead>
            <tr>
                <th>
                    Название
                </th>
                @foreach ($aggregators as $agg)
                    <th>
                        {{$agg->name}}
                    </th>
                @endforeach
                <th>
                    Действия
                </th>
            </tr>
        </thead>
        <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            {{$product->title}}
                        </td>
                        {{--
                            Проверяем есть ли прайс агрегатор
                            В списке прайс агрегаторов продукта
                        --}}
                        @foreach ($aggregators as $agg)
                            @if (in_array($agg->id,array_column($product->aggregators->toArray(),'id')))
                                <td>+</td>
                            @else
                                <td>-</td>
                            @endif
                        @endforeach
                        <td>
                            <a class="btn btn-xs btn-default" href="productdescription/{{$product->id}}/edit/#svyazi">Редактировать</a>
                        </td>
                    </tr>    
                @endforeach
        </tbody>
    </table>
    
@endsection

@section('after_styles')
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/show.css') }}">
@endsection

@section('after_scripts')
	<script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
	<script src="{{ asset('vendor/backpack/crud/js/show.js') }}"></script>
@endsection