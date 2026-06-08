<template>
    <textarea :id="id" :name="name"
        class="form-field"
        :class="{
            'form-field--invalid' : hasErrors,
            'form-field--no-scrollbar' : ! hasScrollbar
        }"
        :maxlength="maxlength"
        :value="value"
        :aria-invalid="hasErrors ? 'true' : null"
        :aria-describedBy="describedBy || null"
        @input="onInput"
    ><slot></slot></textarea>
</template>

<script>
    import FormField from '../../../mixins/FormField.js';

    export default {
        mixins: [FormField],

        props: {
            /**
             * Define the maximum number of characters allowed.
             */
            maxlength: {
                type: String,
                required: false,
                initial: '10000'
            }
        },

        data() {
            return {
                hasScrollbar: false
            };
        },

        /**
         * Set the initial content (if any) and check for the presence
         * of a scrollbar as soon as the component is mounted.
         */
        mounted() {
            this.setInitialContent();

            this.setScrollbarState();
        },

        methods: {
            /**
             * Add the reactive property to the `fields` object in parent form.
             */
            setInitialContent() {
                this.$el.value = this.$slots.default ? this.$slots.default[0].text : '';

                this.$set(this.$parent.fields, this.name, this.$el.value);
            },

            /**
             * Determine whether the textarea currently shows a scrollbar.
             */
            setScrollbarState() {
                this.hasScrollbar = this.$el.scrollHeight >= this.$el.offsetHeight;
            },

            /**
             * Send the field content to the parent form and update the scrollbar
             * visibility state when the user interacts with the textarea.
             */
            onInput(e) {
                this.$emit('input', e.target.value);

                this.setScrollbarState();
            }
        }
    };
</script>
