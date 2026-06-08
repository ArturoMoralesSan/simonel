<template>
    <ul class="dashboard-menu__link-group">
        <li>
            <p class="dashboard-menu__links-title"
                :class="{
                    'dashboard-menu__links-title--current' : submenu.active,
                    'dashboard-menu__links-title--active' : open
                }"
                @click="toggle"
            >
                <img class="dashboard-menu__links-icon" :src="$root.path + '/archivos/dashboard/icons/' + submenu.icon" alt="">
                {{ submenu.name }}
                <span class="dashboard-menu__links-arrow" aria-hidden="true"></span>
            </p>

            <ul class="dashboard-menu__link-list" v-show="open" ref="list">

                <li v-for="(link, i) in submenu.links" :key="i">
                    <a class="dashboard-menu__link"
                        :class="{ 'dashboard-menu__link--active' : link.active }"
                        :href="$root.path + '/admin/' + link.route"
                    >
                        {{ link.name }}
                    </a>
                </li>

            </ul>
        </li>
    </ul>
</template>

<script>
    import { slideToggle } from '../../../helpers/animation/slide.js';

    export default {
        props: {
            submenu: Object
        },

        data() {
            return {
                open: this.submenu.active
            };
        },

        methods: {
            /**
             * Show or hide the list of links with an animation.
             */
            toggle() {
                slideToggle(this.$refs.list);
                this.open = ! this.open;
            }
        }
    };
</script>
