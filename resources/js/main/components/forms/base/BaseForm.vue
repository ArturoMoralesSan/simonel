<script>
    import scrollTo from '../../../../helpers/scrollTo.js';
    import CheckboxField from './CheckboxField.vue';
    import DateField from './DateField.vue';
    import FieldErrors from './FieldErrors.vue';
    import FileField from './FileField.vue';
    import FormButton from './FormButton.vue';
    import SelectField from './SelectField.vue';
    import SearchSelectField from './SearchSelectField.vue';
    import TextArea from './TextArea.vue';
    import TextField from './TextField.vue';

    export default {
        components: {
            CheckboxField,
            FieldErrors,
            FileField,
            FormButton,
            SelectField,
            SearchSelectField,
            TextArea,
            TextField,
            DateField
        },

        props: {
            /**
             * The URI that will recieve the data.
             */
            action: {
                type: String,
                required: true
            },

            /**
             * HTTP method used to submit the form.
             */
            method: {
                type: String,
                required: false,
                default: 'post'
            },

            /**
             * Redirect to the given URI after form submit. This overrides
             * any redirection received as response. Leave empty to ignore.
             */
            redirectTo: {
                type: String,
                required: false,
                default: ''
            },

            /**
             * Space to have as offset between the top of the vieport and the
             * first invalid field when validation fails. Unitless pixel value.
             */
            scrollOffset: {
                type: Number,
                required: false,
                default: 50
            },

            /**
             * Define if the form must perform AJAX validation on submit
             * and display any errors if the validation fails.
             */
            validate: {
                type: Boolean,
                default: true
            },
            /**
             * Define initial values for fields.
             */
            initial: {
                type: Object,
                required: false,
                default: () => { return {}; }
            }
        },

        data() {
            return {
                errors: {},
                groupedErrors: {},
                isSubmitting: false,
                submitError: false,
                supportsDates: null,
                fields: {
                    _method: this.method
                }
            };
        },

        computed: {
            /**
             * Check if form element has an `enctype` property with a value
             * of `multipart/form-data`. If so, a FormData object will be
             * used to send the form via AJAX.
             */
            supportsFiles: function() {
                return this.$el.getAttribute('enctype') === 'multipart/form-data';
            }
        },

        /**
         * Group validation errors by field. Useful when using array fields in
         * form. Example:
         *
         * errors: {
         *     textfield_error: ...
         *     array_error.0: ...
         *     array_error.1: ...
         * }
         *
         * groupedErrors: {
         *     textfield_error: ...
         *     array_error: [
         *         0: ...
         *         1: ...
         *     ]
         * }
         */
        watch: {
            errors: function(value) {
                const regex = new RegExp('\\.\\d+$');
                let groupedErrors = {};

                for (let errorKey in value) {
                    const matches = regex.exec(errorKey);

                    if (matches) {
                        const field = errorKey.replace(regex, '');
                        const index = matches[0].replace('.', '');

                        groupedErrors[field] = groupedErrors[field] || {};
                        groupedErrors[field][index] = value[errorKey];
                    }
                    else {
                        groupedErrors[errorKey] = value[errorKey];
                    }
                }

                this.groupedErrors = groupedErrors;
            }
        },

        /**
         * Set the initial values.
         * Add listener for the submit event.
         */
        mounted() {
            for (let key in this.initial) {
                this.$set(this.fields, key, this.initial[key]);
            }

            this.$el.addEventListener('submit', this.submit);
        },

        methods: {
            /**
             * Submit the form via AJAX.
             */
            submit(e) {
                e.preventDefault();

                this.isSubmitting = true;
                this.submitError = false;

                this.hook('beforeSubmit');

                window.axios.post(
                    this.action,
                    this.supportsFiles ? this.createFormData() : this.fields
                )
                .then(response => {
                    this.errors = {};

                    this.hook('submitSucceeded', response);

                    this.onSuccess(response);
                })
                .catch(this.onSubmitFail);
            },


            /**
             * Return a new FormData object with the information
             * of the `fields` property.
             *
             * If a field has an Array as value, square brackets "[]" are
             * appended to the key name.
             *
             * @return {FormData}
             */
            createFormData() {
                const formData = new FormData();

                for (let key in this.fields) {
                    if (Array.isArray(this.fields[key])) {
                        for (let i = 0; i < this.fields[key].length; i++) {
                            formData.append(key + '[]', this.fields[key][i]);
                        }
                    }
                    else {
                        formData.append(key, this.fields[key]);
                    }
                }

                return formData;
            },


            /**
             * If the failed response has a status code of 422 or 429, handle the
             * display of validation errors according to user options.
             * Otherwise, treat it as a server error.
             *
             * @param {Object} error
             */
            onSubmitFail(error) {
                this.errors = {};
                this.validationCodes = [422, 429];

                this.isSubmitting = false;
                this.submitError = this.validationCodes.indexOf(error.response.status) === -1;

                this.hook('submitFailed', error);

                if (! this.validate || this.submitError) {
                    return;
                }

                this.errors = error.response.data.errors;

                // Wait for `aria-invalid` attributes to be updated
                // before scrolling to the first invalid field
                Vue.nextTick(() => {
                    this.scrollToError();
                    this.hook('afterValidation');
                });
            },


            /**
             * Handle redirection after receiving a successful response.
             *
             * @param {Object} response
             */
            onSuccess(response) {
                if (this.redirectTo) {
                    return window.location.href = this.redirectTo;
                }

                if (response.headers['redirect-to']) {
                    return window.location.href = response.headers['redirect-to'];
                }

                this.isSubmitting = false;
            },


            /**
             * Scroll to the first invalid field in form.
             */
            scrollToError() {
                const invalidField = this.$el.querySelector('[aria-invalid="true"]');

                if (invalidField) {
                    scrollTo(invalidField, -this.scrollOffset);
                    invalidField.focus();
                }
            },


            /**
             * Call a method after verifying that it exists.
             * Useful for registering hooks for use in components that might
             * extend this one.
             *
             * @param {string} method
             * @param {mixed} params
             */
            hook(method, params = null) {
                if (typeof this[method] === 'function') {
                    this[method](params);
                }
            },


            /**
             * Update a form field with the given value.
             *
             * @param  {string} fieldName
             * @param  {mixed} value
             */
            updateField(fieldName, value) {
                this.$set(this.fields, fieldName, value);
            }
        }
    };
</script>
