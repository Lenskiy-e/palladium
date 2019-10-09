<input type="text" name="name" value="{{$user->name}}">
<input type="text" name="last_name" value="{{$profile->last_name}}">
<input type="text" name="patronymic" value="{{$profile->patronymic}}">
<select name="gender">
    <option value="0">Male</option>
    <option @if($profile->gender) selected @endif value="1">Female</option>
</select>
<input type="date" name="birthday" id="birthday" value="{{$profile->birthday}}">