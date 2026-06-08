<template>
    <div class="tabs">
        <tabs-menu v-if="! responsiveSelect || breakpointMatches"
            :active="active"
            :tabs="tabs"
            :breakpointMatches="breakpointMatches"
            :acceptsHtml="acceptsHtml"
            @activate="setActive"
        >
        </tabs-menu>

        <select v-if="responsiveSelect && ! breakpointMatches"
            class="form-field tabs__select"
            @change="setActive($event.target.value)"
        >
            <option v-for="(name, key) in (options || tabs)"
                :key="key"
                :value="key"
                :selected="key === active"
            >
                {{ name }}
            </option>
        </select>

        <tabs-panel v-for="(name, key) in tabs"
            :key="key"
            :slug="key"
            :is-active="key === active"
            :slots="$slots"
        >
            <slot :name="'panel-' + key" :slot="'panel-'+key"></slot>
        </tabs-panel>
    </div>
</template>

<script>
    import TabsMenu from './TabsMenu.vue';
    import TabsPanel from './TabsPanel.vue';

    export default {
        components: { TabsMenu, TabsPanel },

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
             * Viewport breakpoint to change the style to "desktop" view.
             */
            breakpoint: {
                type: Number,
                required: false,
                default: 600
            },

            /**
             * Initial active tab.
             */
            initial: {
                type: String,
                required: false
            },

            /**
             * Options to include in the select element.
             * Used when the text inside the options must be different than the
             * tabs content.
             *
             * Receives an object in "slug: name" format.
             */
            options: {
                type: Object,
                required: false
            },

            /**
             * Tabs to show.
             * Receives an object in "slug: name" format.
             */
            tabs: {
                type: Object,
                required: true
            },

            /**
             * Define if a select element will be used in small screens
             * before the breakpoint is triggered.
             *
             * @type {Object}
             */
            responsiveSelect: {
                type: Boolean,
                required: false,
                default: true
            }
        },

        data() {
            return {
                active: '',
                mq: null,
                breakpointMatches: true
            };
        },

        created() {
            this.setActive(this.initial);

            this.mq = window.matchMedia(`(min-width: ${this.breakpoint}px)`);
            this.mq.addListener(this.breakpointCheck);
            this.breakpointCheck();
        },

        methods: {
            /**
             * Add a "mobile" css class if viewport width is below
             * the breakpoint.
             */
            breakpointCheck() {
                this.breakpointMatches = this.mq.matches;
            },

            /**
             * Set a tab as active.
             *
             * @param {String} slug
             */
            setActive(slug='') {
                this.active = slug || Object.keys(this.tabs)[0];
            }
        }
    };
</script>
