<template>
    <button class=" btn btn-expand-search header__search-button btn--xs"
    @click="toggleMenu"
    >
        <slot name="icon"></slot>
    </button>
</template>

<script>
    export default {
        props: {
            breakpoint: {
                type: Number,
                required: true
            }
        },

        data() {
            return {
                buttonClicked: false,
                menuIsOpen: false,
            };
        },

        mounted() {
            this.setMatchMedia();
        },

        methods: {

            /**
             * Initialize media query.
             */
            setMatchMedia() {
                const mq = window.matchMedia(`screen and (max-width:${this.breakpoint}px)`);

                mq.addListener(this.reset);

                this.reset(mq);
            },


            /**
             * Immediately show or hide menu list when media match is triggered.
             *
             * @param {Object} e
             */
            reset(e) {
                this.menuIsOpen = false;
                this.buttonClicked = false;
                if (e.matches) {
                    document.querySelector('.header__container-form').classList.add('search-hide');
                } else {
                    document.querySelector('.header__container-form').classList.remove('search-hide');
                    document.querySelector('.header__container-form').classList.remove('search-expand');
                }
            },

            /**
             * Close site menu on responsive view.
             */
            closeMenu() {
                this.menuIsOpen = false;
                document.querySelector('.header__container-form').classList.remove('search-expand');
                document.querySelector('.header__container-form').classList.add('search-hide');
            },

            /*
             * Diplay site menu on responsive view.
             */
            openMenu() {
                this.menuIsOpen = true;
                document.querySelector('.header__container-form').classList.remove('search-hide');
                document.querySelector('.header__container-form').classList.add('search-expand');
            },

            /**
             * Toggle menu visibility.
             */
            toggleMenu() {
                this.buttonClicked = true;

                if (this.menuIsOpen) {
                    this.closeMenu();
                }
                else {
                    this.openMenu();
                }
            }
        }
    };
</script>