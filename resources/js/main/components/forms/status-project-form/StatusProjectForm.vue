<template>
    <form v-if="status !== 'Aprobado'">

        <div v-if="status">
            <h1 class="h3 mb-8">
                 Proyecto con folio {{ folio }}
            </h1>

            <p><b>Estado:</b> {{ status }}</p>
            <p v-if="observations"><b>Observaciones:</b> {{ observations }}</p>

            <hr class="my-12">
        </div>

        <div>
            <h1 class="h3 mb-8">
                Consulta {{ status ? 'otro' : 'tu'}} proyecto
            </h1>

            <div class="form-control">
                <label for="folio">Folio</label>
                <text-field name="folio"
                    v-model="fields.folio"
                >
                </text-field>

                <field-errors name="folio"></field-errors>
            </div>
        </div>

        <div class="text-center pt-8">
            <form-button class="btn--primary btn--wide">
                Consultar
            </form-button>
        </div>
   </form>
 </template>

<script>
	import BaseForm from '../base/BaseForm.vue';

	export default {
		extends: BaseForm,

		data() {
			return {
				status: '',
                observations: '',
                folio: ''
			}
		},
		methods: {
			submitSucceeded(response) {
                this.status       = response.data.status;
                this.observations = response.data.observations;
                this.folio        = response.data.folio;
			}
		}
	}
</script>