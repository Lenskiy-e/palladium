@extends('backpack::layout')

@section('header')

@endsection

@section('content')
<form class="form-vertical col-md-12" method="GET">
        <div class="form-group col-md-6">
            <label>Показать изменения с:</label>
        <input type="date" value="{{$data['from'] ?? ''}}" name="log_from" class="form-controll col-md-12">
        </div>
        <div class="form-group col-md-6">
                <label>Показать изменения по:</label>
                <input type="date" value="{{$data['to'] ?? ''}}" name="log_to" class="form-controll col-md-12">
        </div>
        <div class="form-group col-md-12">
            <input type="submit" value="Найти" class=" btn btn-success pull-right"/>
        </div>
    </form>
<div>
    @foreach ($data['rows'] as $logs)
        @foreach ($logs as $log)
            <h2>Дата: {{$log->created_at->format('d.m.Y H:i:s')}}</h2>
            <h3>Отредактировал: {{$log->getUser->name}}</h3>
                <div style="width:50%; float:left;">
                    <h3>Было</h3>
                    @foreach (json_decode($log['old']) as $key => $value)
                        <b>{{$key}}</b>
                        @if (in_array($key,$data['translation']))
                            @foreach (json_decode($value) as $lang => $val)
                                <p><b>{{$lang}}: </b> {{$val}}</p>
                            @endforeach 
                        @else
                            <p>{{$value}}</p>
                        @endif
                    @endforeach
                </div>
                <div style="width:50%; float:right;">
                    <h3>Стало</h3>
                    @foreach (json_decode($log['new']) as $key => $value)
                        <b>{{$key}}</b>
                        @if (in_array($key,$data['translation']))
                            @foreach (json_decode($value) as $lang => $val)
                                <p><b>{{$lang}}: </b> {{$val}}</p>
                            @endforeach 
                        @else
                            <p>{{$value}}</p>
                        @endif
                    @endforeach
                </div>
        @endforeach
    @endforeach
</div>
@endsection


@section('after_styles')
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/show.css') }}">
@endsection

@section('after_scripts')
	<script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
	<script src="{{ asset('vendor/backpack/crud/js/show.js') }}"></script>
@endsection
