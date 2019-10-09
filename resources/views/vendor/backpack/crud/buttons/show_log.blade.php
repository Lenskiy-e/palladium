<!-- 
**
* Кнопка вызова лога
**
-->

@if (backpack_user()->hasPermissionTo('show log'))
    <div class="btn-group">
    <a class="btn btn-xs btn-default" href="{{$crud->logFrom}}/{{ $entry->id }}/show_log"><i class="fa fa-user"></i>Лог</a>
    </div>
@endif