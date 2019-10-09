<div id="filters">
    {{--Как у разведки--}}
    <div id="active">
        @if($actives)
            @foreach ($actives as $id => $param)
            <div class="param" id="head-param-{{$id}}">
                <span>{{$param}}</span>
                <label class="filter_delete" data-id="{{$id}}">X</label>
            </div>
            @endforeach
        @endif
    </div>
    @foreach ($model->filters as $attr)
        {{-- TODO --}}
        {{-- переписать активный атрибут не через жопу --}}
        <div class="filter-title @if($attr->parameter->whereIn('id', $active_filters)->count()) filter-param-open @endif">{{$attr->title}}</div>
        <ul class="filter-param">
            @foreach ($attr->parameter as $param)
                <li class="filter-param-item">
                    <label class="checkbox {{$products_count[$param->id] ? '' : 'no-active'}}"
                           for="param-{{$param->id}}">
                        @if ($attr->prefix)
                            <span class="prefix">{{$attr->prefix}}</span>
                        @endif
                        <span class="filter-param-name">{{$param->name}} <span class="count">({{$products_count[$param->id]}})</span></span>

                        @if ($attr->sufix)
                            <span class="sufix">{{$attr->sufix}}</span>
                        @endif
                        <input type="checkbox" id="param-{{$param->id}}"
                               @if(in_array($param->id, $active_filters)) checked @endif name="param[]"
                               {{$products_count[$param->id] ? '' : 'disabled'}} value="{{$param->id}}">
                        <span class="check-mark"></span>
                    </label>
                </li>
            @endforeach
        </ul>

    @endforeach
</div>
