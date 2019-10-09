<!-- 
**
* Кнопка аналогична стандартной update
* Отличается тем, что проверяет назначен
* ли этот товар под данного менеджера
**
-->

@if (!$entry->edit_staus && !$entry->manager_id)
    @if ($crud->hasAccess('update'))
        <!-- Edit button group -->
        <div class="btn-group">
        <button data-product="{{ $entry->id }}" class="appoint btn btn-xs btn-default"><i class="fa fa-user"></i> {{ trans('backpack::crud.appoint') }}</button>
        </div>

        <script>
            $('.appoint').on('click', function(){
                $.ajax({
                    url: 'productdescription/{{$entry->id}}/appoint',
                    method: 'post',
                    data: {
                            "manager_id": {{ backpack_user()->id }},
                            "product_id": $(this).data('product'), 
                            "_token": $('meta[name="csrf-token"]').attr('content')
                           },
                    success: function(){
                        location.reload();
                    }
                })
            });
        </script>
    @endif
@endif