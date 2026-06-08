<template>
    <div v-show="isActive"
        :id="'panel-' + slug"
        class="tabs__panel"
        role="tabpanel"
        :aria-labelledby="'tab-' + slug"
        :aria-hidden="isActive ? 'false' : 'true'"
    >
        <slot :name="'panel-' + slug"></slot>
    </div>
</template>

<script>
    export default {
        props: {
            /**
             * Key used to link the panel with its tab.
             */
            slug: {
                type: String,
                required: true
            },

            /**
             * Define if panel is currently active/visible.
             */
            isActive: {
                type: Boolean,
                required: false,
                default: false
            }
        }
    };
</script>
