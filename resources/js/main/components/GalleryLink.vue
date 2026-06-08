<template>
    <a @click.prevent="openGallery">
        <slot></slot>
    </a>
</template>

<script>
    import * as merge from 'deepmerge';

    export default {
        props: {
            /**
             * Gallery slug for displaying multiple images inside gallery.
             * Don't pass this if you want to display only the image
             * defined in the 'href' property.
             */
            gallery: {
                type: String,
                required: false,
            },

            /**
             * Options to pass to PhotoSwipe.
             * Accepts two extra properties, `width` and `height`,
             * for single image galleries.
             */
            options: {
                type: Object,
                required: false,
                default: () => { return {}; }
            }
        },

        data() {
            return {
                activeThumb: null,
                container: document.querySelector('#photoswipe'),
                config: {
                    closeOnScroll    : false,
                    loop             : false,
                    shareEl          : false,
                    showHideOpacity  : true,
                    getThumbBoundsFn : this.getThumbCoordinates,
                    errorMsg : '<div class="pswp__error-msg">No se encontró <a href="%url%" target="_blank">la imagen</a>.</div>'
                }
            };
        },

        methods: {
            /**
             * Return thumb coordinates for opening animation.
             *
             * @return {Object}
             */
            getThumbCoordinates() {
                const rect = this.$el.getBoundingClientRect();

                return {
                    x: rect.left,
                    y: rect.top + (window.pageYOffset || document.documentElement.scrollTop),
                    w: rect.width
                };
            },


            /**
             * Initialize the gallery.
             */
            openGallery() {
                this.fetchItems()
                    .then(items => {
                        var photoswipe = new window.PhotoSwipe(
                            this.container,
                            window.PhotoSwipeUI_Default,
                            items,
                            merge(this.config, this.options)
                        );

                        photoswipe.init();
                    });
            },


            /**
             * Return the normalized list of images for PhotoSwipe.
             *
             * @return {array}
             */
            fetchItems() {
                return new Promise((resolve, reject) => {
                    if (! this.gallery) {
                        resolve ([
                            {
                                src: this.$el.getAttribute('href'),
                                w: this.options.width,
                                h: this.options.height
                            }
                        ]);
                    }

                    window.axios.get(this.$root.path + '/api/archivos/noticias/' + this.gallery)
                    .then(response => {
                        resolve(response.data);
                    });
                });
            }
        }
    };
</script>
