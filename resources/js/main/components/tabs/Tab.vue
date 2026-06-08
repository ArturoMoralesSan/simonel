<template>
    <li :id="'tab-' + slug"
        class="tabs__li"
        :class="{ 'tabs__li--active' : isActive }"
        role="tab"
        :aria-controls="'panel-' + slug"
        :aria-selected="isActive ? 'true' : 'false'"
        :tabindex="isActive ? '0' : '-1'"
        @click="$emit('activate', slug)"
        @keyup.enter="$emit('activate', slug)"
        @keydown.left.up.prevent="focusSibling('previous')"
        @keydown.right.down.prevent="focusSibling()"
    >
        <span v-if="acceptsHtml" v-html="name">
        </span>

        <template v-else>
            {{ name }}
        </template>
    </li>
</template>

<script>
    export default {
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
             * Key to identify the tab.
             */
            slug: {
                type: String,
                required: true
            },

            /**
             * Text to display inside tab.
             */
            name: {
                type: String,
                required: true
            },

            /**
             * If tab is currently active.
             */
            isActive: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        methods: {
            /**
             * Set the focus on a subling tab.
             *
             * @param {String} direction
             */
            focusSibling(direction='next') {
                const sibling = direction === 'next' ?
                    this.$el.nextElementSibling :
                    this.$el.previousElementSibling;

                if (sibling) {
                    sibling.focus();
                }
            }
        }
    };
</script>
