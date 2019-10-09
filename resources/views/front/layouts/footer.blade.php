<footer id="footer">
    <div class="wrapper">
        <ul class="list-default">
            @forelse($footer_links as $link)
                <li class="list-item-default">
                    <a href="/{{$link['slug']}}" class="@activeClass($link['slug'])">
                        {{$link['name']}}
                    </a>
                </li>
            @empty

            @endforelse
        </ul>
    </div>
</footer>
@include('front.layouts.right-menu')
<div id="modal"></div>
<div id="overlay"></div>
<script src="/libs/svg4everybody/svg4everybody.min.js"></script>
<script>
    svg4everybody();
</script>