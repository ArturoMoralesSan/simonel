<template>
    <div class="box box--lg bg-white b-1 rounded relative">

        <h3>
            Materia prima {{ index }}
        </h3>

        <button
            v-if="index > minItem"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('removeP', index)"
        >
            Eliminar
        </button>

        <div class="row mb-4">
            <div class="md:col">
                <div class="form-control">
                    <label>Materia prima</label>

                    <select-field
                        :name="'item' + index + '_raw_material_id'"
                        v-model="fields['item' + index + '_raw_material_id']"
                        :options="materials"
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined')
                            ? assignedMaterials[index-1].raw_material_id.toString()
                            : '')"
                    >
                    </select-field>

                    <field-errors
                        :name="'item' + index + '_raw_material_id'"
                    ></field-errors>
                </div>
            </div>
        </div>

        <div class="row mb-4">

            <div class="md:col-1/3">
                <div class="form-control">
                    <label>Cantidad</label>

                    <text-field
                        :name="'item' + index + '_quantity'"
                        v-model="fields['item' + index + '_quantity']"
                        maxlength="20"
                        type="number"
                        step="any"
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined')
                            ? assignedMaterials[index-1].quantity
                            : '')"
                    >
                    </text-field>

                    <field-errors
                        :name="'item' + index + '_quantity'"
                    ></field-errors>
                </div>
            </div>

            <div class="md:col-1/3">
                <div class="form-control">
                    <label>Costo unitario</label>

                    <text-field
                        :name="'item' + index + '_unit_cost'"
                        v-model="fields['item' + index + '_unit_cost']"
                        maxlength="20"
                        type="number"
                        step="0.0001"
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined')
                            ? assignedMaterials[index-1].unit_cost
                            : '')"
                    >
                    </text-field>

                    <field-errors
                        :name="'item' + index + '_unit_cost'"
                    ></field-errors>
                </div>
            </div>

            <div class="md:col-1/3">
                <div class="form-control">
                    <label>Total</label>

                    <text-field
                        :name="'item' + index + '_total_cost'"
                        :value="totalItem(index)"
                        disabled
                    >
                    </text-field>
                </div>
            </div>

        </div>

        <div class="row mb-4">

            <div class="md:col-1/3">
                <div class="form-control">
                    <label>Lote proveedor</label>

                    <text-field
                        :name="'item' + index + '_supplier_lot'"
                        v-model="fields['item' + index + '_supplier_lot']"
                        maxlength="100"
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined')
                            ? assignedMaterials[index-1].supplier_lot.toString()
                            : '')"
                    >
                    </text-field>

                    <field-errors
                        :name="'item' + index + '_supplier_lot'"
                    ></field-errors>
                </div>
            </div>

            <div class="md:col-1/3">
                <div class="form-control">
                    <label>Caducidad</label>
                    <text-field
                        type="date"
                        :name="'item' + index + '_expiration_date'"
                        v-model="fields['item' + index + '_expiration_date']"
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined')
                            ? assignedMaterials[index-1].expiration_date_input
                            : '')"
                    >
                    </text-field>

                    <field-errors
                        :name="'item' + index + '_expiration_date'"
                    ></field-errors>
                </div>
            </div>
            <div class="md:col-1/3">
                <div class="form-control">
                    <label>Almacen</label>

                    <select-field
                        :name="'item' + index + '_warehouse_id'"
                        v-model="fields['item' + index + '_warehouse_id']"
                        :options="warehouses"
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined')
                            ? assignedMaterials[index-1].warehouse_id.toString()
                            : '')"
                    >
                    </select-field>

                    <field-errors
                        :name="'item' + index + '_warehouse_id'"
                    ></field-errors>
                </div>
            </div>

        </div>

    </div>
</template>

<script>
    import SelectField from '../base/SelectField.vue';
    import DateField from '../base/DateField.vue';
    import TextField from '../base/TextField.vue';
    import FieldErrors from '../base/FieldErrors.vue';

    export default {
        components: { DateField, SelectField, FieldErrors, TextField },

        props: {
            materials: {
                type: [Object, Array],
                required: true
            },
            warehouses: {
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
            
            minItem: {
                required: true,
                type: Number
            },
            assignedMaterials: {
                required: true,
                type: Array
            },
        },
        data() {
            return {
                noExistAlert: false,

            };
        },
        methods: {
            totalItem(index) {
                const quantity = parseFloat(
                    this.fields['item' + index + '_quantity'] || 0
                );

                const unitCost = parseFloat(
                    this.fields['item' + index + '_unit_cost'] || 0
                );

                return (quantity * unitCost).toFixed(2);
            },

            formatDate(date) {
                if (!date) {
                    return '';
                }

                if (date.includes('/')) {
                    const [day, month, year] = date.split('/');
                    return `${year}-${month}-${day}`;
                }

                return date.substring(0, 10);
            },

            totalItem(index) {
                const quantity = parseFloat(
                    this.fields['item' + index + '_quantity'] || 0
                );

                const unitCost = parseFloat(
                    this.fields['item' + index + '_unit_cost'] || 0
                );

                return (quantity * unitCost).toFixed(2);
            }
        }
    };
</script>
