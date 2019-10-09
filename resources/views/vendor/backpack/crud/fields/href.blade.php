<!-- text input -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')

    <div class="input-group">
        <a target="__blank" href="{{ square_brackets_to_dots($field['value'])}}">{{ square_brackets_to_dots($field['value'])}}</a>
    </div>
</div>