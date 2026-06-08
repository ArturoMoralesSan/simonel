<template>
    <input :id="id" :name="name"
        class="form-field"
        :class="{ 'form-field--invalid' : hasErrors }"
        type="date"
        maxlength="10"
        :aria-invalid="hasErrors ? 'true' : null"
        :aria-describedBy="describedBy || null"
        v-model="displayValue"
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
            }
        },

        data() {
            return {
                displayValue: ''
            };
        },

        watch: {
            displayValue: function(value) {
                this.$emit('input', this.formatDate(value));
            }
        },

        created() {
            if (this.$parent.supportsDates === null) {
                this.$parent.supportsDates = this.checkBrowserSupport();
            }
        },

        /**
         * Set an initial value to the field.
         */
        mounted() {
            this.displayValue = this.formatDate(this.initial, true);
        },

        methods: {
            /**
             * Determine if browser supports native `date` type fields.
             *
             * @return boolean
             */
            checkBrowserSupport() {
                const input = document.createElement('input');

                input.setAttribute('type', 'date');
                input.setAttribute('value', 'invalid-date');

                return input.value !== 'invalid-date';
            },


            /**
             * Normalize date for browsers that don't have support
             * for native `date` fields.
             *
             * @param  {string} date
             * @param  {boolean} dmyFormat - Return the date in dd/mm/yyyy format.
             * @return {string}
             */
            formatDate(date, dmyFormat=false) {
                if (this.$parent.supportsDates || ! date) {
                    return date;
                }

                const regex = dmyFormat ?
                    /^(\d{4})-(\d{2})-(\d{2})$/ :
                    /^(\d{2})\/(\d{2})\/(\d{4})$/;

                const separator = dmyFormat ? '/' : '-';
                const match = regex.exec(date);

                return match ? match[3] + separator + match[2] + separator + match[1] : date;
            }
        }
    };
</script>
