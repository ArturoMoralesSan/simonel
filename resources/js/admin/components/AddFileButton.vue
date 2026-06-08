<template>
    <button :class="classes" type="button"
        @click.prevent="showConfirmation"
    >
        <slot>Agregar</slot>
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
                default: 'btn btn--sm btn--update'
            },
            resourceId: {
                type: Number,
                required: false
            },
            method: {
                type: String,
                required: false
            },
            title: {
                type: String,
                required: false
            },
            nombre: {
                type: String,
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
                    },
                    metodos : {
                        put : {
                            title             : this.title,
                            cancelButtonText  : 'Cancelar',
                            confirmText       : 'Aceptar',
                            errorText         : 'Mensaje recibido:',
                            errorTitle        : 'No se pudo actualizar.',
                            successText       : 'Fue actualizado',
                            successTitle      : 'Actualizado'
                        },
                        post : {
                            title             : this.title,
                            cancelButtonText  : 'Cancelar',
                            confirmText       : 'Aceptar',
                            errorText         : 'Mensaje recibido:',
                            errorTitle        : 'No se pudo agregar.',
                            successText       : 'Fue agregado',
                            successTitle      : 'Agregado'
                        }
                    },
                    lang : document.documentElement.lang.toLowerCase().substr(0, 2)
                },
                settings: {},
                metodos: {}
            };
        },

        created() {
            this.settings = merge(this.defaults, this.options || {});
            this.metodos = this.settings.metodos[this.method];
        },

        methods: {
            /**
             * Make the ajax call to Add the resource.
             *
             * @return Promise
             */
            AddResource() {
                const form = document.getElementById('modal-form');

                return new Promise((resolve, reject) => {

                    window.axios.post(this.url, new FormData(form))
                        .then(response => {
                            if (response.status === 200 || response.status === 204) {
                                return resolve(response);
                            }

                            this.showError(response.statusText);
                        })
                        .catch(error => {
                            if (error.response.status == 422) {
                                this.validateInput(error.response.data);
                                window.swal.disableLoading();
                                window.swal.enableConfirmButton();
                            } else {
                                this.showError(error);
                            }
                        });
                });
            },

            formhtml() {
                const html = `<form id="modal-form" enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="`+this.method+`" />
                            <div class="form-control">
                                <label for="modal-name">Nombre del archivo:</label>
                                <input id="modal-name" class="form-field" type="text" name="filename" value="`+this.nombre+`" maxlength="100">
                                <ul id="filename-specs" class="specs description"></ul>
                            </div>
                            <div class="form-control">
                                <label for="modal-file">Archivo:</label>
                                <input id="modal-file" class="form-field" type="file" name="file">
                                <ul id="max-specs" style="margin-bottom: 0px;" class="description">
                                    <li>
                                        2 MB máximo por archivo
                                    </li>
                                </ul>
                                <ul id="file-specs" class="specs description"></ul>
                            </div>
                        </form>`;
                return html;
            },

            delValidation(el) {
                var element = document.querySelectorAll('.'+el+'-validate');
                if (element.length > 0) {
                    Array.prototype.forEach.call(element, function(node) {
                        node.parentNode.removeChild(node);
                    });
                }
            },

            addvalidation(el,error) {
                let specs = document.getElementById(el+'-specs');
                var li    = document.createElement('li');
                li.appendChild(document.createTextNode(error.errors[el]));
                li.classList.add('text--danger');
                li.classList.add(el+'-validate');
                specs.appendChild(li);
            },


            validateInput(error) {
                var uls = document.querySelectorAll('.specs');
                    Array.prototype.forEach.call(uls, function(elem) {
                        elem.innerHTML = '';
                    });
                for (var el in error.errors) {
                    this.delValidation(el);
                    this.addvalidation(el, error);
                }
            },

            /**
             * Emit event for the parent to add the created item to the resource list.
             *
             * @param {array} item
             */
            addItem(item) {
                this.$emit('newfile', item);
            },

            /**
             * Emit event for the parent to update item to the resource list.
             *
             * @param {array} item
             */
            updateItem(item) {
                this.$emit('editfile', item);
            },


            /**
             * Display a warning message before deleting the resource.
             *
             * @param {Object} e
             */
            showConfirmation(e) {
                window.swal({
                    title               : this.metodos.title,
                    text                : this.metodos.text,
                    html                : this.formhtml(),
                    allowOutsideClick   : false,
                    showCancelButton    : true,
                    cancelButtonText    : this.metodos.cancelButtonText,
                    confirmButtonColor  : this.settings.colors.confirm,
                    confirmButtonText   : this.metodos.confirmText,
                    showLoaderOnConfirm : true,
                    preConfirm          : this.AddResource.bind(this)
                })
                .then(response => {
                    if (response.dismiss) {
                        return;
                    }
                    if (this.method == 'post') {
                        this.addItem(response.value.data);
                    }
                    if (this.method == 'put') {
                        this.updateItem(response.value.data);
                    }

                });

                const form = document.getElementById('modal-form');

                form.addEventListener('submit', function(evt){
                    evt.preventDefault();
                });
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
                    title              : this.metodos.errorTitle,
                    text               : this.metodos.errorText + ' ' + error,
                    type               : 'error',
                    confirmButtonColor : this.settings.colors.confirm,
                    confirmButtonText  : this.metodos.confirmText
                });
            },


            /**
             * Show a "success" modal.
             */
            showSuccess() {
                window.swal({
                    title              : this.metodos.successTitle,
                    text               : this.metodos.successText,
                    type               : 'success',
                    confirmButtonColor : this.settings.colors.confirm,
                    confirmButtonText  : this.metodos.confirmText
                });
            }
        }
    };
</script>
