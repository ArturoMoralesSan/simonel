import * as merge from 'deepmerge';

export default {
    props: {
        images: {
            type: Array,
            required: true
        },
        options: {
            type: Object,
            required: false
        },
        path: {
            type: String,
            required: false,
            default: 'img/galeria/'
        },
        useThumb: {
            type: Boolean,
            required: false,
            default: true
        }
    },

    data() {
        return {
            activeThumb: null,
            dom: {
                galleryContainer: {},
            },
            config: {
                closeOnScroll : false,
                loop          : false,
                shareEl       : false,
                showHideOpacity: true,
                errorMsg      : '<div class="pswp__error-msg">No se encontró <a href="%url%" target="_blank">la imagen</a>.</div>',
                getThumbBoundsFn: null
            }
        };
    },

    computed: {
        /**
         * Make sure the gallery path ends with a slash.
         *
         * @return string
         */
        galleryPath: function() {
            return this.path.slice(-1) === '/' ? this.path : this.path + '/';
        },

        /**
         * Create the images object for Photoswipe.
         *
         * @return {Object}
         */
        items: function() {
            let items = [];

            for (let i in this.images) {
                items.push({
                    src: this.galleryPath + this.images[i].img,
                    h: this.images[i].height,
                    w: this.images[i].width
                });
            }

            return items;
        }
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
            const rect = this.activeThumb.getBoundingClientRect();

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
            this.activeThumb = e.currentTarget.querySelector('.js-thumb');

            const gallery = new window.PhotoSwipe(
                this.dom.galleryContainer,
                window.PhotoSwipeUI_Default,
                this.items,
                merge(this.config, {
                    getThumbBoundsFn: this.useThumb ? this.getThumbCoordinates : null,
                    index: parseInt(e.currentTarget.getAttribute('data-index') || 0, 10)
                })
            );

            gallery.init();
        }
    }
};
