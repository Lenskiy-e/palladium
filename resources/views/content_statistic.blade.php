@extends('backpack::layout')

@section('header')
	<section class="content-header">
	  <h1>
        <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
        <small>{!! $crud->getSubheading() ?? mb_ucfirst(trans('backpack::crud.preview')).' '.$crud->entity_name !!}.</small>
      </h1>
	  <ol class="breadcrumb">
	    <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
	    <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
	    <li class="active">{{ trans('backpack::crud.preview') }}</li>
	  </ol>
	</section>
@endsection

@section('content')
<h2>Наполнено - {{$crud->total_count}} товар(ов)</h2>
	@if ($crud->total_count < $crud->plan)
		<h3>Количество товаров до выполнения плана: {{$crud->plan}}</h3>
	@else
		<h3>План - {{$crud->plan}} товар(ов)</h3>
		<h3>Сверх плана - {{$crud->total_count - $crud->plan}} товар(ов)</h3>
	@endif
	@if ($crud->overplan_products)
		<h4>Заполненные карточки товаров (свыше плана)</h4>
		<table class="table table-bordered">
			<thead>
				<tr class="success">
					<td><b>Название товара</b></td>
					<td><b>грн за него</b></td>
				</tr>
			</thead>
			<tbody>
				@foreach ($crud->overplan_products as $p)
					<tr class="info">
						<td>{{$p->title}}</td>
						<td>{{$p->mainCategory->categoryCost[0]->cost}} грн</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@endif

@endsection


@section('after_styles')
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/show.css') }}">
@endsection

@section('after_scripts')
	<script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
	<script src="{{ asset('vendor/backpack/crud/js/show.js') }}"></script>
@endsection
