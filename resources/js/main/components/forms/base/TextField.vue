<template>
    <input :id="id" :name="name"
        class="form-field"
        :class="{ 'form-field--invalid' : hasErrors }"
        :type="type"
        :maxlength="maxlength"
        :value="value"
        :aria-invalid="hasErrors ? 'true' : null"
        :aria-describedBy="describedBy || null"
        @input="$emit('input', $event.target.value)"
    >
</template>

<script>
    import FormField from '../../../mixins/FormField.js';

    export default {
        mixins: [FormField],

        props: {
            /**
             * Receive an initial value for the field.
             */
            initial: {
                type: String,
                required: false,
                default: ''
            },

            /**
             * Define the maximum number of characters allowed.
             */
            maxlength: {
                type: String,
                required: false,
                default: '255'
            },

            /**
             * Define the value for the `type` attribute of the input
             * (e.g. text, email).
             */
            type: {
                type: String,
                required: false,
                default: 'text'
            }
        },

        /**
         * Add the reactive property to the `fields` object in parent form.
         */
        mounted() {
            this.$set(this.$parent.fields, this.name, this.initial);
        }
    };
</script>
