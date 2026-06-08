<template>
    <a class="gallery-card" :href="link" @click.prevent="onClick">
        <div class="gallery-card__img-container" ref="thumb">
            <img class="gallery-card__img" :src="thumb" alt="">
        </div>
        Ver imágenes
    </a>
</template>

<script>
    import GalleryMixin from '../mixins/ProfileGalleryMixin.js';

    export default {
        mixins: [GalleryMixin],

        props: {
            thumb: {
                type: String,
                required: true
            },
            link: {
                type: String,
                required: true
            }
        }
    };
</script>
