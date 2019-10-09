@foreach ($hobbies as $hobby)
    <label>{{$hobby->title}}</label>
    <input name="hobbies[]" type="checkbox" value="{{$hobby->id}}" @if(in_array($hobby->id, $active_hobbies)) checked="checked"  @endif>
@endforeach
