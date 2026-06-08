<template>
    <ul v-if="errors" :id="id" class="error">
        <li v-for="(error, i) in errors" :key="i" v-text="error"></li>
    </ul>
</template>

<script>
    export default {
        props: {
            /**
             * The `name` value of the associated field.
             */
            name: {
                type: String,
                required: true
            },

            /**
             * Define if the component should merge the errors received
             * for an array field.
             */
            array: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        computed: {
            /**
             * Fetch the validation errors for the associated field
             * from the parent form component.
             *
             * @return {array}
             */
            errors() {
                if (! this.array) {
                    return this.$parent.errors[this.name];
                }

                let regex = new RegExp('^' + this.name + '\\.\\d+$');
                let errors = [];

                for (let field in this.$parent.errors) {
                    if (regex.test(field)) {
                        this.$parent.errors[field].forEach(error => {
                            if (errors.indexOf(error) === -1) {
                                errors.push(error);
                            }
                        });
                    }
                }

                return errors;
            },

            /**
             * Return a formatted ID for the error list.
             *
             * @return {string}
             */
            id() {
                return this.$options.filters.slugify(this.name + '-errors');
            }
        }
    };
</script>
