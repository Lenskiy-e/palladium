<!-- select2 -->
@php
    $current_value = $entry->manager_id ?? '';
    $options = $crud->managers;
@endphp

<div @include('crud::inc.field_wrapper_attributes') >

    <label>Контент-менеджер</label>
    <select
        data-product="{{ $entry->id }}"
        name="user_id"
        @include('crud::inc.field_attributes', ['default_class' =>  'users input-sm select2 form-control select2_field'])
        >
            <option value="">-</option>
        @if (count($options))
            @foreach ($options as $option)
                @if($current_value == $option->getKey())
                    <option value="{{ $option->getKey() }}" selected>{{ $option->name }}</option>
                @else
<option value="{{ $option->getKey() }}">{{ $option->name }}</option>
                @endif
            @endforeach
        @endif
    </select>
</div>

