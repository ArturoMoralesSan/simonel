<template>
    <button :class="classes" type="button"
        @click.prevent="showConfirmation"
    >
        <slot>Eliminar</slot>
    </button>
</template>

<script>
    import merge from 'deepmerge';

    export default {
        props: {
            url: {
                type: String,
                required: true
            },
            classes: {
                type: String,
                required: false,
                default: 'btn btn--sm btn-nowrap btn--delete'
            },
            resourceId: {
                type: Number,
                required: false
            },
            options: {
                type: Object,
                required: false
            }
        },

        data() {
            return {
                defaults: {
                    colors : {
                        confirm : '#5a8',
                        delete  : '#c44'
                    },
                    localization : {
                        en : {
                            title             : 'Do you want to delete this item?',
                            text              : 'You won\'t be able to revert this',
                            cancelButtonText  : 'Cancel',
                            confirmText       : 'OK',
                            deleteText        : 'Delete',
                            errorText         : 'Message received:',
                            errorTitle        : 'The item could not be deleted.',
                            successText       : 'Deleted',
                            successTitle      : 'The item was deleted'
                        },
                        es : {
                            title             : '¿Deseas eliminar este elemento?',
                            text              : 'Esta acción no se puede deshacer',
                            cancelButtonText  : 'Cancelar',
                            confirmText       : 'Aceptar',
                            deleteText        : 'Eliminar',
                            errorText         : 'Mensaje recibido:',
                            errorTitle        : 'No se pudo eliminar el elemento.',
                            successText       : 'El elemento fue eliminado',
                            successTitle      : 'Eliminado'
                        }
                    },
                    lang : document.documentElement.lang.toLowerCase().substr(0, 2),
                    onDelete : this.onDelete
                },
                settings: {},
                localization: {}
            };
        },

        created() {
            this.settings = merge(this.defaults, this.options || {});

            this.localization = this.settings.localization[this.settings.lang] || this.settings.localization.en;
        },

        methods: {
            /**
             * Make the ajax call to delete the resource.
             *
             * @return Promise
             */
            deleteResource() {
                return new Promise((resolve, reject) => {
                    window.axios.delete(this.url)
                        .then(response => {
                            if (response.status === 204) {
                                return resolve();
                            }

                            this.showError(response.statusText);
                        })
                        .catch(this.showError.bind(this));
                });
            },


            /**
             * Callback to be fired after item was deleted.
             * A possible use could be to remove element from DOM.
             *
             * @param {DeleteButton} DeleteButton - Receive the component instance
             *     as parameter for external use.
             */
            onDelete(DeleteButton) {
                DeleteButton.showSuccess();
            },


            /**
             * Display a warning message before deleting the resource.
             *
             * @param {Object} e
             */
            showConfirmation(e) {
                window.swal({
                    title               : this.localization.title,
                    text                : this.localization.text,
                    type                : 'warning',
                    showCancelButton    : true,
                    cancelButtonText    : this.localization.cancelButtonText,
                    confirmButtonColor  : this.settings.colors.delete,
                    confirmButtonText   : this.localization.deleteText,
                    showLoaderOnConfirm : true,
                    preConfirm          : this.deleteResource.bind(this)
                })
                .then((response) => {
                    if (response.dismiss) {
                        return;
                    }

                    this.settings.onDelete(this)
                })
            },


            /*
            |------------------------------------------------------------------------
            | State messages
            |------------------------------------------------------------------------
            */

            /**
             * Display the received error from the ajax call in a modal.
             *
             * @param {string} error
             */
            showError(error) {
                window.swal({
                    title              : this.localization.errorTitle,
                    text               : this.localization.errorText + ' ' + error,
                    type               : 'error',
                    confirmButtonColor : this.settings.colors.confirm,
                    confirmButtonText  : this.localization.confirmText
                });
            },


            /**
             * Show a "success" modal.
             */
            showSuccess() {
                window.swal({
                    title              : this.localization.successTitle,
                    text               : this.localization.successText,
                    type               : 'success',
                    confirmButtonColor : this.settings.colors.confirm,
                    confirmButtonText  : this.localization.confirmText
                });
            }
        }
    };
</script>
