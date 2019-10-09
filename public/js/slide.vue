<carousel>
    <slide>
        Slide 1 Content
    </slide>
    <slide>
        Slide 2 Content
    </slide>
</carousel>

<script>
    import Vue from 'vue';
    import VueCarousel from 'vue-carousel';

    Vue.use(VueCarousel);


    import {Carousel, Slide} from 'vue-carousel';

    export default {

        components: {
            Carousel,
            Slide
        }

    };
</script>