<template>
    <div class="db-panel">
        <p class=db-panel__title>
            Estado
        </p>

        <dl class="list-inline list-inline--md">
            <dt>
                Estado:
            </dt>
            <dd v-if="permission_project || statusKey === 'approved' || statusKey === 'not-approved'">
                {{ status }}
            </dd>
            <dd v-else>
                Asignado
            </dd>
        </dl>
        <dl v-if="statusKey !== 'received' && permission_project " class="">
            <dt>
                Evaluadores:
            </dt>
            <dd v-for="reviewerEl in listReviewer" :key="reviewerEl">
                {{ reviewerEl }}
            </dd>
        </dl>
        <form v-if="(statusKey === 'received' || statusKey === 'evaluation') && permission_project && reviewer_conut">
            <div class="form-control">
                <label for="reviewer">
                    <strong>Asignar evaluador:</strong>
                </label>

                <select-field
                    name="reviewer"
                    v-model="fields.reviewer"
                    ref="select"
                    :options="reviewer_pluck"
                >
                </select-field>

                <field-errors name="reviewer"></field-errors>
            </div>

            <div class="text-center">
                <form-button class="btn--primary"
                    :disabled="! fields.reviewer"
                >
                    Asignar
                </form-button>
            </div>
        </form>
    </div>
</template>
<script>
    import BaseForm from '../../../main/components/forms/base/BaseForm.vue';

    export default {
        extends: BaseForm,

        props: {
            articleCode: {
                type: String,
                required: true
            },

            asignedusers: {
                type: Array,
                required: false
            },

            reviewers: {
                type: Object,
                required: true
            },

            status_project: {
                type:Object,
                required:true
            },

            permission_project: {
                type:String,
                required:true
            },
            reviewer_user: {
                type:Boolean,
                required:true
            },
            reviewers_count:{
                type:Number,
                required:true
            }
        },
        data() {
            return {
                listReviewer: this.asignedusers,
                status: this.status_project.name,
                statusKey: this.status_project.key_name,
                reviewer_pluck: this.reviewers,
                reviewer_conut: this.reviewers_count
            };
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

                this.confirm();
            },


            /**
             * Display a conformation message before assigning the reviewer.
             */
            confirm() {
                const reviewer = this.$refs.select.options[this.fields.reviewer];

                window.swal.fire({
                    type: 'warning',
                    html: `<p class="mb-1">¿Deseas asignar a <strong>${reviewer}</strong> como evaluador(a)?</p>`,
                    showCancelButton: true,
                    preConfirm: this.approve,
                    showLoaderOnConfirm: true
                })
                .then((response) => {
                    if (response.dismiss || response.value.status === 422) {
                        this.isSubmitting = false;
                        return;
                    }

                    if (response.value.status === 200 || response.value.status === 204) {

                        window.swal.fire({
                            type: 'success',
                            title: 'Evaluador asignado',
                            text: 'Se ha enviado un correo de notificación al evaluador.'
                        });

                        this.listReviewer.push(response.value.data.reviewer_name);
                        this.status    = response.value.data.status;
                        this.statusKey =  response.value.data.status_key;
                        this.reviewer_pluck = response.value.data.reviewerList;
                        this.reviewer_conut = response.value.data.reviewers_count

                        return;
                    }

                    window.swal.fire({
                        type: 'error',
                        html: `<p>Ocurrió un problema al guardar los datos.</p><small class="color-gray-darken-2">${response.value.statusText}</small>`
                    });
                });
            },


            /**
             * Make the AJAX request.
             */
            approve() {
                return window.axios.post(
                    this.action,
                    this.supportsFiles ? this.createFormData() : this.fields
                )
                .then(response => {
                    this.errors = {};

                    this.hook('submitSucceeded', response);

                    this.onSuccess(response);

                    return response;
                })
                .catch((error) => {
                    this.onSubmitFail(error);

                    return error.response;
                });
            }
        }
    };
</script>
