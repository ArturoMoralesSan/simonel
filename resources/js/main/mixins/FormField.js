export default {
    props: {
        /**
         * A list of IDs of the elements that describe the field
         * according to ARIA specifications, separated by a space.
         */
        ariaDescribedby: {
            type: String,
            required: false,
            default: ''
        },

        /**
         * Define the `name` for the field.
         */
        name: {
            type: String,
            required: true
        },

        /**
         * Declaring the value as a property is necessary for binding
         * the data inside custom input components.
         */
        value: {
            required: false
        }
    },

    computed: {
        /**
         * Add the ID of the validation error to the `aria-describedby`
         * attribute if validation fails.
         *
         * @return {string}
         */
        describedBy() {
            if (! this.$parent.validate) {
                return this.ariaDescribedby;
            }

            return this.hasErrors ? `${this.id}-errors ${this.ariaDescribedby}` : this.ariaDescribedby;
        },

        /**
         * Check if the field has any errors.
         *
         * @return {boolean}
         */
        hasErrors() {
            // Remove the "[]" ending from multiplevalue fields, like "images[]".
            const fieldName = this.name.replace(/\[]$/, '');

            return !! this.$parent.errors[fieldName];
        },

        /**
         * Return a formatted ID for the field.
         *
         * @return {string}
         */
        id() {
            return this.$options.filters.slugify(this.name);
        }
    }
};
