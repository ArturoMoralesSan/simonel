<template>
    <div class="form-file">
        <input :id="id" :name="name"
            class="form-file__upload"
            type="file"
            tabindex="0"
            :multiple="multiple"
            :aria-invalid="hasErrors ? 'true' : null"
            :aria-describedBy="describedBy || null"
            ref="input"
            @change="onChange"
            @focus="hasFocus = true"
            @blur="hasFocus = false"
        >

        <input class="form-file__text"
            :class="{
                'form-field--invalid' : hasErrors,
                'form-field--focus' : hasFocus
            }"
            type="text"
            :placeholder="placeholder"
            :value="inputText"
            tabindex="-1"
        >

        <button class="form-file__btn btn btn--light"
            type="button"
            @click="$refs.input.click()"
        >
            Examinar
        </button>
    </div>
</template>

<script>
    import FormField from '../../../mixins/FormField.js';

    export default {
        mixins: [FormField],

        model: {
            event: 'attached'
        },

        props: {
            /**
             * Define if multiple files can be uploaded.
             */
            multiple: {
                type: Boolean,
                required: false,
                default: false
            },

            /**
             * A placeholder to show in the text input when no file
             * has been selected yet.
             */
            placeholder: {
                type: String,
                required: false,
                default: 'Ningún archivo seleccionado.'
            }
        },

        data() {
            return {
                attachments: [],
                hasFocus: false
            };
        },

        computed: {
            /**
             * The text to be shown in input when files are attached.
             *
             * @return string
             */
            inputText() {
                switch(this.attachments.length) {
                    case 0:
                        return '';
                    case 1:
                        return this.attachments[0].name;
                    default:
                        return this.attachments.length + ' archivos seleccionados.';
                }
            }
        },

        methods: {
            /**
             * Update the attachments.
             *
             * @param {Object} e
             */
            onChange(e) {
                let attachments = [];

                Array.prototype.forEach.call(e.target.files, file => {
                    attachments.push(file);
                });

                this.attachments = attachments;

                this.$emit(
                    'attached',
                    this.multiple ? this.attachments : this.attachments[0]
                );
            }
        }
    };
</script>
