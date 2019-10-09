@foreach ($categories as $category)
    <p>
        <input type="checkbox" @if(in_array($category->id, $active_filters)) checked @endif name="param[]" value="{{$category->id}}">
        {{$category->title}} ({{$category->activeProducts()->count()}})
    </p>
@endforeach
