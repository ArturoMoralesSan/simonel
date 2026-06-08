<template>
    <div class="gallery-panel__item"
        :class="{ 'gallery-panel__item--invalid' : isInvalid }"
    >
        <button class="gallery-panel__close-btn"
            type="button"
            @click="$emit('remove', index)"
        >
            <span class="visually-hidden">Eliminar imagen</span>
        </button>

        <img class="gallery-panel__thumb" :src="thumb" alt="">
    </div>
</template>

<script>
    export default {
        props: {
            /**
             * Position of the component in the item list.
             */
            index: {
                type: Number,
                required: true
            },

            /**
             * Specify if item failed validation.
             */
            isInvalid: {
                type: Boolean,
                required: true
            },

            /**
             * The thumb path for already stored images.
             * A File object for new attached files.
             */
            file: {
                type: [File, String],
                required: true
            }
        },

        data() {
            return {
                thumb: false
            };
        },

        created() {
            this.setThumb();
        },

        methods: {
            /**
             * Create a thumbnail image of the attached file as a base64 encoded
             * string to be used in `src` attribute.
             *
             * If the item is not an attached file, simply return the path
             * to its thumbnail.
             */
            setThumb() {
                if (typeof this.file === 'string') {
                    return this.thumb = this.file;
                }

                const reader = new FileReader();

                reader.addEventListener('load', e => {
                    this.thumb = reader.result;
                });

                reader.readAsDataURL(this.file);
            }
        }
    };
</script>
