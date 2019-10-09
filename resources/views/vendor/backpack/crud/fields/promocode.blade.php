<!-- text input -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
        <input
            type="text"
            name="{{ $field['name'] }}"
            value="{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}"
            @include('crud::inc.field_attributes')
        >
        <button id="generate_code">Сгенерировать</button>
</div>

@push('crud_fields_scripts')
<script>
    $("#generate_code").on('click', function(e){
        e.preventDefault();
        let code = Math.random().toString(36).substring(2, 5) + Math.random().toString(36).substring(2, 5);

        $("input[name='code']").val(code);
    });
</script>
@endpush
