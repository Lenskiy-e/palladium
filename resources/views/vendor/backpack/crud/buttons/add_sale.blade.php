<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i><span> Добавить</span></button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">Выбор сделай свой</h4>
            </div>
            <div class="modal-body">
              <h4>Выберите тип</h4>
              <select class="form-control" id="type">
                    <option value="-">-</option>
                    <option value="content">Акция (с контентом)</option>
                    <option value="empty">Скидка (без контента)</option>
                </select>
                <div id="type_form">
                </div>
            </div>
            <div class="modal-footer">
              <a style="display:none;" type="button" href="sales/create" id="create_button" class="btn btn-primary">Создать</a>
              <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
          </div>
        </div>
      </div>

@push('crud_list_scripts')
    <script>
        var button_href = "{{ url('admin/sales/create') }}";
        var get_param = '';
        // При выборе типа создаваемой акции
        $("#type").change(function(){
            /*
            ** Если опция пустая: прячем кнопку и очищаем
            **  блок под селектом
            */
            if($(this).val() == "-"){
                $("#create_button").hide();
                $("#type_form").empty();
            }
            /*
            ** Если нет, то показываем кноку
            ** В случае, если длина массива
            ** больше нуля - прячем кнопку
            ** и создаем селект с набором опций
            ** из скрипта \app\Http\Controllers\Api\SaleController
            */
            else{
                $("#create_button").show();
                $("#type_form").empty();
                get_param = '?type=' + $(this).val();
                $.ajax({
                    url: "{{ url('api/type') }}",
                    type: "post",
                    data: {"type" : $(this).val()},
                    success: function(data){
                        if (data.length > 0) {
                            var type_options = [$('<option/>',{
                                        text: '-',
                                        value: '-'
                                    })];
                            var discount_select = $('<select/>',{
                                id: 'discount_type',
                                class: 'form-control',
                            });
                            $("#create_button").hide();
                            
                            for(var key in data){
                                type_options.push(
                                    $('<option/>',{
                                        text: data[key].text,
                                        value: data[key].value
                                    })
                                );
                            }
                            discount_select.html(type_options);
                            $("#type_form").html(discount_select);
                        }
                    }
                })
                $("#create_button").attr('href', button_href + get_param);
            }
        });

        $(document).on("change","#discount_type",function(){
            get_param = '?type=' + $(this).val();
            $("#create_button").show();
            $("#create_button").attr('href',  button_href + get_param);
        });
    </script>  
@endpush

