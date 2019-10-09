<div class="breadcrumbs">
    @foreach ($breadcrumbs as $breadcrumb)
        <a href="{{$breadcrumb['href']}}">{{$breadcrumb['name']}}</a>
        <b> |</b>
    @endforeach
    <span>{{$breadcrumb['name']}}</span>
</div>