<template>
    <a class="gallery"
        @click.prevent="load"
    >
        <div class="gallery__cover-container">
            <span class="gallery__counter">
                <span class="gallery__counter-number">{{ imagesCount }}</span> imágenes
            </span>
            <img class="js-thumb gallery__cover" :src="$root.path + '/img/galeria/' + cover">
        </div>
        {{ title }}
    </a>
</template>

<script>
    import GalleryMixin from '../mixins/Gallerymixin.js';

    export default {
        mixins: [GalleryMixin],

        props: {
            slug: {
                type: String,
                required: true
            },

            title: {
                type: String,
                required: true
            },

            cover: {
                type: String,
                required: true
            },

            images: {
                type: Array,
                required: false,
                default: () => []
            },

            imagesCount: {
                type: Number,
                required: true
            }
        },

        data() {
            return {
                loadedImages: []
            };
        },

        computed: {
            /**
             * Create the images object for Photoswipe.
             *
             * @return {Object}
             */
            items: function() {
                let items = [];

                for (let i in this.loadedImages) {
                    items.push({
                        src: this.galleryPath + this.loadedImages[i].src,
                        h: this.loadedImages[i].height,
                        w: this.loadedImages[i].width
                    });
                }

                return items;
            }
        },

        methods: {
            /**
             * Fetch the gallery images.
             *
             * @param {Object} e
             */
            load(e) {
                // currentTarget is lost after propagation, so using a variable
                // to store the value is neccesary
                const event = { currentTarget: e.currentTarget };

                axios.get(this.$root.path + '/galerias/' + this.slug)
                    .then(response => {
                        this.loadedImages = response.data;
                        this.onClick(event);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        }
    };
</script>
