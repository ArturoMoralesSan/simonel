<template>
    <header class="main-header" role="banner">
        <div class="container header__container">
            <a class="main-header__logo-link" :href="uri" title="Inicio">
                <img class="main-header__logo-img" :src="logo" alt="">
            </a>
            <p v-if="breakpointMatches" class="main-header-text">
                Sistema Gestor de Inventario y Préstamos
            </p>
            <p v-else class="main-header-text">
                Sistema Gestor de Inventario y Préstamos
            </p>
            <div class="login-container">
                <slot name="button-session"></slot>

            </div>
        </div>
    </header>
</template>

<script>
    export default {
        props: {
            uri: {
                type:String,
                required: true
            },
            logo: {
                type:String,
                required: true
            },
            breakpoint: {
                type: Number,
                required: true
            }
        },

        data() {
            return {
                breakpointMatches: false,
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
                const mq = window.matchMedia(`screen and (min-width:${this.breakpoint}px)`);

                mq.addListener(this.reset);

                this.reset(mq);
            },


            /*
            |------------------------------------------------------------------------
            | Visibility
            |------------------------------------------------------------------------
            */

            /**
             * Immediately show or hide menu list when media match is triggered.
             *
             * @param {Object} e
             */
            reset(e) {
                this.breakpointMatches = e.matches;
            }
        }
    };
</script>
