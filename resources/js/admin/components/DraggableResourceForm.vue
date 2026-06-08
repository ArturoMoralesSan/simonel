<template>
    <div>
        <slot name="table"
            :wideView="wideView"
            :resourceList="resourceList"
            :onResourceDelete="onResourceDelete"
            :forceFallback="forceFallback"
        ></slot>

        <p v-if="submitError" class="color-danger size-caption text-center">
            {{ submitError }}
        </p>

        <form class="text-center" @submit.prevent="submit">
            <slot name="button">
                <form-button class="btn btn--dashboard" slot="button" :is-submitting="isSubmitting">
                    <slot name="button-text">
                        Actualizar orden
                    </slot>
                </form-button>
            </slot>
        </form>
    </div>
</template>

<script>
    import ResourceTable from './ResourceTable.vue';

    export default {
        extends: ResourceTable,

        props: {
            action: {
                required: true
            },
            method: {
                default: 'post',
                required: false
            },
            modelName: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                submitError: '',
                forceFallback: navigator.userAgent.toLowerCase().indexOf('firefox') > -1,
                formData: [],
                isSubmitting: false
            };
        },

        watch: {
            resourceList: function() {
                this.setFormData();
            }
        },

        mounted() {
            this.setFormData();
        },

        methods: {
            /**
             * Prepare the data that will be sent though ajax.
             */
            setFormData() {
                this.formData = {
                    _method: this.method
                };

                this.formData[this.modelName] = this.resourceList.map((item, i) => {
                    return {
                        order: i + 1,
                        id: item.id
                    };
                });
            },


            /**
             * Show a message after updating the order of the elements.
             */
            successMessage() {
                window.swal({
                    title: 'Se actualizaron los elementos.',
                    type: 'success',
                    confirmButtonText: 'Aceptar'
                });
            },

            /**
             * Submit the changes.
             */
            submit() {
                if (this.isSubmitting) {
                    return;
                }

                this.isSubmitting = true;
                this.submitError = '';

                window.axios.post(this.action, this.formData)
                    .then(response => {
                        this.isSubmitting = false;
                        this.successMessage();
                    })
                    .catch(error => {
                        this.isSubmitting = false;

                        this.submitError = 'Hubo un problema al enviar la información. Por favor intenta de nuevo más tarde.'
                    });
            }
        }
    };
</script>
