<div id="homeSlide" class="slide">
    <div class="slide-items owl-carousel owl-theme">
        <div class="slide-item" data-hash="image-item-1">
            <a href="#">
                <div class="img-wrap"><img src="uploads/bunners/home-banner.jpg" alt="" title=""></div>
            </a>
        </div>
        <div class="slide-item" data-hash="image-item-2">
            <a href="#">
                <div class="img-wrap"><img src="uploads/bunners/home-banner.jpg" alt="" title=""></div>
            </a>
        </div>
    </div>
</div>

<div class="slide-hash">
    <div class="slide-hash-item active">
        <a href="#image-item-1" style="background-image: url('uploads/bunners/home-banner.jpg');"></a>
    </div>
    <div class="slide-hash-item">
        <a href="#image-item-2" style="background-image: url('uploads/bunners/home-banner.jpg');"></a>
    </div>
</div>

<script>
    $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        items: 1,
        URLhashListener: true,
        autoplayHoverPause: true,
        startPosition: 'URLHash',
        autoplay: true,
        autoplayTimeout: 5000,
    })
</script>