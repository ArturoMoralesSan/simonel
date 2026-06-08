<template>
    <ul class="tabs__menu"
        :class="{ 'tabs__menu--desktop-view' : breakpointMatches }"
        role="tablist"
    >
        <li is="tab"
            v-for="(name, key) in tabs"
            :key="key"
            :name="name"
            :slug="key"
            :isActive="key === active"
            :acceptsHtml="acceptsHtml"
            @activate="(slug) => $emit('activate', slug)"
        >
        </li>
    </ul>
</template>

<script>
    import Tab from './Tab.vue';

    export default {
        components: { Tab },

        props: {
            /**
             * Define if tabs content is rendered as raw HTML.
             * Warning: never render raw HTML on user-provider-content,
             * as it may lead to XSS vulnerabilities.
             */
            acceptsHtml: {
                type: Boolean,
                required: false,
                default: false
            },

            /**
             * The slug/key of active tab.
             */
            active: {
                type: String,
                required: false
            },

            /**
             * If viewport width is longer than the specified
             * component breakpoint.
             */
            breakpointMatches: {
                type: Boolean,
                required: false,
                default: false
            },

            /**
             * Tabs to show.
             * Receives an object in "slug: name" format.
             */
            tabs: {
                type: Object,
                required: true
            }
        }
    };
</script>
