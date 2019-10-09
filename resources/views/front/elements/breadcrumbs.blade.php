<div class="breadcrumbs">
    @foreach ($breadcrumbs as $element)
    @if($element['href'])
        <a href="{{$element['href']}}">{{$element['title']}}</a>
        <b>|</b>
    @else
        <span>{{$element['title']}}</span>
        @endif
        @endforeach
</div>
