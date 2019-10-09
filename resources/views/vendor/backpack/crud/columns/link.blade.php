{{-- regular object attribute --}}
@php
	$value = data_get($entry, $column['name']);

	if (is_array($value)) {
		$value = json_encode($value);
	}
@endphp

<a href="{{strip_tags($value)}}" target="_blank">{{strip_tags($value)}}</a>