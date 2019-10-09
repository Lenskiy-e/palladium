<label for="children">Наличие детей</label><input type="checkbox" @if ($marketing->children) checked @endif name="children" id="children" {{$profile->children ? 'checked' : ''}}>

<div id="children_age" @if(!$marketing->children) style="display:none;" @endif>
    @forelse ($user->children as $child)
        <div>
            <span onclick="deleteChild($(this).parent('div'))">X</span>
            <span>{{$child->age}}</span>
            <input type="hidden" name="child_age[]" value="{{$child->age}}">
        </div>

    @empty
        Добавьте возраст детей
    @endforelse
    <input type="text" name="add_children">
    <button id="add_child">Добавить</button>

</div>

{{-- Вынести в скрипты --}}
<script>
    $("#children").change(function(){
        if ($("#children:checked").length) 
        {
            $("#children_age").show(200);
        }else
        {
            $("#children_age").hide(200);
        }
    });

    $("#add_child").click(function(e){
        e.preventDefault();

        var age = $("input[name='add_children']");
        if(age.val().length > 0)
        {
            var child_text = $("<span/>",{
                class:  'child_element',
                text:   age.val()
            });

            var child = $("<input/>",{
                type:   'hidden',
                name:   'child_age[]',
                val:    age.val()
            });
            age.val('');
            $("#children_age").append([child, child_text]);
        }

    });

    function deleteChild(element)
    {
        element.remove();
    }
</script>
