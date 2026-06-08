import merge from 'deepmerge';

export default {
    props: {
        images: {
            type: Array,
            required: true
        },
        options: {
            type: Object,
            required: false
        }
    },

    data() {
        return {
            dom: {
                galleryContainer: {},
            },
            config: {
                closeOnScroll : false,
                loop          : false,
                shareEl       : false,
                showHideOpacity: true,
                errorMsg      : '<div class="pswp__error-msg">No se encontró <a href="%url%" target="_blank">la imagen</a>.</div>',
                getThumbBoundsFn: this.getThumbCoordinates
            }
        };
    },

    mounted() {
        this.config = merge(this.config, this.options || {});

        this.dom.galleryContainer = document.querySelector('#photoswipe');
    },

    methods: {
         /**
         * Return thumb coordinates for opening animation.
         *
         * @return {Object}
         */
        getThumbCoordinates() {
            const rect = this.$refs.thumb.getBoundingClientRect();

            return {
                x: rect.left,
                y: rect.top + (window.pageYOffset || document.documentElement.scrollTop),
                w: rect.width
            };
        },


        /**
         * Build the gallery when the thumb is clicked.
         *
         * @param {Object}
         */
        onClick(e) {
            const gallery = new window.PhotoSwipe(
                this.dom.galleryContainer,
                window.PhotoSwipeUI_Default,
                this.images,
                this.config
            );

            gallery.init();
        }
    }
};
