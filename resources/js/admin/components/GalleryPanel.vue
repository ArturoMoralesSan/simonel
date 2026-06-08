<template>
    <div class="gallery-panel">
        <draggable v-model="items"
            :options="{
                animation: 200,
                filter: '.gallery-panel__close-btn'
            }"
        >
            <transition-group tag="div"
                class="gallery-panel__showcase"
                name="gallery-panel__item-"
            >
                <gallery-panel-item v-for="(item, index) in items"
                    :key="item.id"
                    :isInvalid="invalidItem(index)"
                    :index="index"
                    :file="item.file || item.thumb"
                    @remove='removeItem'
                >
                </gallery-panel-item>
            </transition-group>
        </draggable>

        <p v-if="items.length > 1" class="description">
            Arrastra las miniaturas para cambiar el orden en el que se mostrarán en la galería.
        </p>

        <div class="gallery-panel__dropzone"
            @dragenter.stop.prevent
            @dragover.stop.prevent
            @dragleave.stop.prevent
            @drop.stop.prevent="onAttach"
        >
            <label :for="inputId" class="gallery-panel__label" ref="test">
                Suelta imágenes aquí o haz <strong>click</strong> para agregar.
            </label>

            <input class="hidden"
                :id="inputId"
                type="file"
                multiple
                @change="onAttach"
            >
        </div>
    </div>
</template>

<script>
    import GalleryPanelItem from './GalleryPanelItem.vue';

    export default {
        components: { GalleryPanelItem },

        model: {
            event: 'attached'
        },

        props: {
            /**
             * Behaviour is disabled when form is being submitted.
             */
            disabled: {
                type: Boolean,
                required: false,
                dafault: false
            },

            /**
             * List of validation errors grouped by type.
             */
            errors: {
                type: Object,
                required: false,
                default: () => {
                    return {
                        id: [],
                        mimes: []
                    };
                }
            },

            /**
             * List of already uploaded images. Each item must include the
             * element ID and a thumb property with the image path.
             */
            initial: {
                type: Array,
                required: false,
                default: () => []
            },

            /**
             * Define the `name` for the component.
             */
            name: {
                type: String,
                required: true
            },

            /**
             * This property receives the synced model from the parent form
             * that contains the indexes of both stored and uploading images.
             */
            order: {
                type: Array,
                required: true,
                default: () => []
            }
        },

        data() {
            return {
                items: [],
                files: [],
                fileIndexes: []
            };
        },

        computed: {
            /**
             * ID value for the file type input.
             *
             * @return {string}
             */
            inputId: function() {
                return this.$options.filters.slugify(this.name) + '-input';
            }
        },

        watch: {
            /**
             * Emit the proper events to update the `files` and `order` fields
             * each time the `items` array changes.
             *
             * The `files` field is an array containing only the files uploaded
             * by the user.
             * The `order` field is an array with the ordered IDs of the already
             * uploaded items and specifies the location for the new files using
             * a 'files' placeholder. i.e:
             * [3, 1, 'file', 2, 'file'];
             */
            items: function(value) {
                let order = [];
                this.files = [];
                this.fileIndexes = [];

                this.items.forEach((item, index) => {
                    if (item.file) {
                        this.files.push(item.file);
                        this.fileIndexes.push(index);
                        order.push('file');
                    }
                    else {
                        order.push(item.id);
                    }
                });

                this.$emit('update:order', order);
                this.$emit('attached', this.files);
            },

            /**
             * Create a list of indexes of the items that failed validation.
             */
            errors: function(newErrors, oldErrors) {
                if (JSON.stringify(newErrors) === JSON.stringify(oldErrors)) {
                    return;
                }

                /*
                 * Get the index of invalid items.
                 */
                let invalidItems = [];

                for (let errorKey in newErrors.id || {}) {
                    invalidItems.push(parseInt(errorKey, 10));
                }

                for (let errorKey in newErrors.mime || {}) {
                    const index = this.fileIndexes[errorKey];

                    if (invalidItems.indexOf(index) === -1) {
                        invalidItems.push(index);
                    }
                }

                /*
                 * Identify the invalid items.
                 */
                this.items.forEach((item, index) => {
                    item.isInvalid = invalidItems.indexOf(index) !== -1;
                });

                /*
                 * Force a refresh on items to show validation errors on submit.
                 */
                const items = this.items;

                this.items = [];
                this.items = items;
            }
        },

        /**
         * Set the initial items, if any.
         */
        mounted() {
            this.items = this.initial;
        },

        methods: {
            /**
             * Add the attached file to the list of items.
             *
             * @param {Object} e
             */
            onAttach(e) {
                const handler = e.dataTransfer || e.target;

                Array.prototype.forEach.call(handler.files, file => {
                    this.items.push({
                        id: this.items.length + '-' + Date.now() + '-' + file.name,
                        file: file
                    });
                });
            },


            /**
             * Remove an item from the gallery.
             *
             * @param {integer} index
             */
            removeItem(index) {
                this.items.splice(index, 1);
            },


            invalidItem(index) {
                return this.items[index].isInvalid || false;
            }
        }
    };
</script>
