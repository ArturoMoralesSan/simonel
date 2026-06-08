<template>
    <div class="box box--lg bg-white b-1 rounded relative">
        <h3>
            Método de pago {{ index }}
        </h3>

        <button v-if="index > minPayment"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('removeP', index)"
        >
            Eliminar
        </button>
        <div class="row mb-4">
            <div class="md:col-1/2 sm:col">
                <div class="form-control">
                    <label :for="'payment' + index + '-pago'">Pago</label>
                    <select-field
                    :name="'payment' + index + '_pago'"
                    v-model="fields['payment' + index + '_pago']"
                    :options="PaymentsData"
                    :initial="((typeof assignedPayments[index-1] !== 'undefined') ? assignedPayments[index-1].id.toString() : '')"
                    >
                    </select-field>
                    <field-errors :name="'payment' + index + '_pago'"></field-errors>
                </div>
            </div>
            <div class="md:col-1/2 sm:col">
                <div class="form-control">
                    <label for="cost">Monto</label>
                    <text-field 
                        class="field-get-researcher" 
                        :name="'payment' + index + '_cost'" 
                        v-model="fields['payment' + index + '_cost']" 
                        maxlength="20" 
                        :initial="((typeof assignedPayments[index-1] !== 'undefined') ? assignedPayments[index-1].pivot.cost.toString(): '')"
                    ></text-field>
                    <field-errors :name="'payment' + index + '_cost'"></field-errors>

                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
    import SelectField from '../base/SelectField.vue';
    import TextField from '../base/TextField.vue';
    import FieldErrors from '../base/FieldErrors.vue';

    export default {
        components: { SelectField, FieldErrors, TextField },

        props: {
            PaymentsData: {
                type: [Object, Array],
                required: true
            },
            index: {
                type: Number,
                required: true
            },

            errors: {
                type: Object,
                required: true
            },

            fields: {
                type: Object,
                required: true
            },
            
            minPayment: {
                required: true,
                type: Number
            },
            assignedPayments: {
                required: true,
                type: Array
            },
        },
        data() {
            return {
                noExistAlert: false,

            };
        },
    };
</script>
