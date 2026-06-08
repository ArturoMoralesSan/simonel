<template>
    <select :id="id" :name="name"
        class="form-field"
        :class="{ 'form-field--invalid' : hasErrors }"
        :value="value"
        :aria-invalid="hasErrors ? 'true' : null"
        :aria-describedBy="describedBy || null"
        @input="$emit('input', $event.target.value)"
    >
        <option value="" disabled><slot>- Selecciona una opción -</slot></option>

        <option v-for="(option, value) in options"
            :key="value"
            :value="value"
            v-text="option"
        >
        </option>
    </select>
</template>

<script>
    import FormField from '../../../mixins/FormField.js';

    export default {
        mixins: [FormField],

        props: {
            /**
             * Receive an initial selected value.
             */
            initial: {
                type: [Number, String],
                required: false,
                default: ''
            },

            /**
             * An object of values and descriptions to populate the options
             * inside the select field.
             */
            options: {
                type: [Object, Array],
                required: true
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
