@php
    $sets = $field['sets'] ?? [];
    $discount_types = [
        1 => '%',
        2 => 'грн.'
    ];
    $sets_counter = 1;
    $blocks_counter = 1;
@endphp

<div id="sets" @include('crud::inc.field_wrapper_attributes') >
    @forelse ($sets as $set)
        <div class="cloneable" style="border: 1px solid black; padding: 5px;">
            <div class="form-group delbut">
                <button class="btn btn-danger delBlock">Удалить комплект</button>
            </div>
            <div class="form-group new_price">
                <label>Стоимость комплекта</label>
                <input type="text" pattern="[0-9]+\.[0-9]?{2}" value="{{$set['new_price']}}" name="set[{{$sets_counter}}][new_price]" class="form-control">
            </div>
            @foreach ($set->product as $product)
                <div class="form-inline added">
                    <div class="form-group">
                        <label>Товар:</label>
                        <input type="hidden" name="set[{{$sets_counter}}][block][{{$blocks_counter}}][product]" value="{{$product->id}}" class="form-control">
                        <input type="text" name="product" value="{{$product->title}}" class="form-control">
                        <ul class="product_list"></ul>
                    </div>
                    <div class="form-group">
                        <label>Тип скидки: {{$product->pivot->discount_type}}</label>
                        <select name="set[{{$sets_counter}}][block][{{$blocks_counter}}][type]" class="form-control">
                            <option value="">-</option>
                            @foreach ($discount_types as $key => $value)
                                @if ($key == $product->pivot->discount_type)
                                <option selected value="{{$key}}">{{$value}}</option>
                                @else
                                <option value="{{$key}}">{{$value}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Сумма скидки:</label>
                        <input type="text" value="{{$product->pivot->product_discount}}" name="set[{{$sets_counter}}][block][{{$blocks_counter}}][discount]" pattern="[0-9]+\.[0-9]?{2}" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-default addBlock" type="button"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-sm btn-default removeBlock" type="button"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                @php
                    $blocks_counter++;
                @endphp
            @endforeach
        </div>
        @php
            $sets_counter++;
        @endphp
    @empty
        <div class="cloneable" style="border: 1px solid black; padding: 5px;">
            <div class="form-group delbut">
                <button class="btn btn-danger delBlock">Удалить комплект</button>
            </div>
            <div class="form-group new_price">
                <label>Стоимость комплекта</label>
                <input type="text" pattern="[0-9]+\.[0-9]?{2}" name="set[0][new_price]" class="form-control">
            </div>
            <div class="form-inline added">
                <div class="form-group">
                    <label>Товар:</label>
                    <input type="hidden" name="set[0][block][0][product]" class="form-control">
                    <input type="text" name="product" autocomplete="off" class="form-control">
                    <ul class="product_list"></ul>
                </div>
                <div class="form-group">
                    <label>Тип скидки:</label>
                    <select name="set[0][block][0][type]" class="form-control">
                        <option value="">-</option>
                        <option value="0">%</option>
                        <option value="1">грн.</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Сумма скидки:</label>
                    <input type="text" name="set[0][block][0][discount]" pattern="[0-9]+\.[0-9]?{2}" class="form-control">
                </div>
                <div class="form-group">
                    <button class="btn btn-sm btn-default addBlock" type="button"><i class="fa fa-plus"></i></button>
                    <button class="btn btn-sm btn-default removeBlock" type="button"><i class="fa fa-minus"></i></button>
                </div>
            </div>
        </div>
    @endforelse
    <button class="btn btn-primary" id="addBlock">Добавить комплект</button>
</div>

@push('crud_fields_scripts')
<!-- Скрипт добавляет поле в комплект -->
<script>
    // Считает кол-во блоков
    var counter = $('.added').length + 1;
    $(document).on('click', '.addBlock', function(){
        set_block = $(this).closest('.cloneable');
        productBlock = $(this).closest('.added');
        var new_block = productBlock.clone();
        new_block.find('.form-control').each(function(){
            var i_name = $(this).attr('name');
            $(this).val('');
            $(this).attr('name',i_name.replace(/(set\[[0-9]+\]\[block\])(\[)([0-9]+)(\])(.*)/,'$1$2' + counter + '$4$5'));
        });
        new_block.appendTo(set_block);
        counter++;
    
    })

    $(document).on('click', '.removeBlock', function(){
        productBlock = $(this).closest('.added');
        productBlock.remove();
    })
    
</script>

<!-- Скрипт добавляет комплект -->
<script>
    $("#addBlock").on('click', function(e){
        e.preventDefault();
        var blocs_counter = $(".cloneable").length;
        var new_set_inputs = [];
        new_block = $(".cloneable").first().clone().empty();
        new_block.append($('.delbut').first().clone());

        new_price_block = $('.new_price').first().clone();
        new_price_input = new_price_block.find('input').attr('name');
        new_price_block.find('input').attr('name',new_price_input.replace(/(set\[)([0-9]+)(.*)/,'$1' + blocs_counter + '$3')).val('');
        new_block.append(new_price_block);
        inputs = $(".added").first().clone();
        inputs.find('.form-control').each(function(){
            var i_name = $(this).attr('name');
            $(this).val('');
            $(this).attr('name',i_name.replace(/(set\[)([0-9]+)(\]\[block\]\[)([0-9]+)(.*)/,'$1' + blocs_counter + '$3' + 1 + '$5'));
        });
        new_block.append(inputs);
        $(new_block).insertBefore("#addBlock");
    });

    $(document).on('click', '.delBlock', function(){
        setBlock = $(this).closest('.cloneable');
        setBlock.remove();
    })
</script>

<!-- Скрипт поиска и добавления товаров -->
<script>
    $(document).on('input','input[name="product"]',function(){
        var search_row = $(this);
        var product_list = $(this).closest('.form-group').find('ul');
        $.ajax({
            type: "get",
            url: "{{url('api/product')}}",
            data: {'q':search_row.val()},
            success: function (data) {
                products_options = [];
                for(var k in data){
                    products_options.push(
                        $('<li/>',{
                            class: 'p_item',
                            'data-id': data[k].product_id,
                            text: data[k].product_name
                        })
                    );
                }
                
                product_list.html(products_options);
                
            }
        });
    });

    $(document).on('click', '.p_item', function(){
        var item = $(this);
        item.closest('.form-group').find('input[type="hidden"]').val(item.data('id'));
        item.closest('.form-group').find('input[name="product"]').val(item.text());
        item.parent().empty();
    });
</script>
@endpush