<template>
    <div class="box box--lg bg-white b-1 rounded relative">
        <h3>
            Ingrediente {{ index }}
        </h3>

        <button v-if="index > minItem"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('removeP', index)"
        >
            Eliminar
        </button>
        <div class="row mb-4">
            <div class="md:col sm:col">
                <div class="form-control">
                    <label :for="'item' + index + '-material'">Materia prima</label>
                    <select-field
                    :name="'item' + index + '_raw_material_id'"
                    v-model="fields['item' + index + '_raw_material_id']"
                    :options="materials"
                    :initial="((typeof assignedMaterials[index-1] !== 'undefined') ? assignedMaterials[index-1].raw_material_id.toString() : '')"
                    >
                    </select-field>
                    <field-errors :name="'item' + index + '_raw_material_id'"></field-errors>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="md:col-1/3 sm:col">
                <div class="form-control">
                    <label for="quantity">Cantidad</label>
                    <text-field 
                        :name="'item' + index + '_quantity'" 
                        v-model="fields['item' + index + '_quantity']" 
                        maxlength="20" 
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined') ? assignedMaterials[index-1].quantity.toString(): '')"
                    ></text-field>
                    <field-errors :name="'item' + index + '_quantity'"></field-errors>

                </div>
            </div>
            <div class="md:col-1/3 sm:col">
                <div class="form-control">
                    <label for="unit">Unidad</label>
                    <text-field 
                        :name="'item' + index + '_unit'" 
                        v-model="fields['item' + index + '_unit']" 
                        maxlength="20" 
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined') ? assignedMaterials[index-1].unit.toString(): '')"
                    ></text-field>
                    <field-errors :name="'item' + index + '_unit'"></field-errors>

                </div>
            </div>
            <div class="md:col-1/3 sm:col">
                <div class="form-control">
                    <label for="waste_percent">Porcentaje de perdida <span class="description">%</span></label>
                    <text-field 
                        :name="'item' + index + '_waste_percent'" 
                        v-model="fields['item' + index + '_waste_percent']" 
                        maxlength="20" 
                        :initial="((typeof assignedMaterials[index-1] !== 'undefined') ? assignedMaterials[index-1].waste_percent.toString(): '')"
                    ></text-field>
                    <field-errors :name="'item' + index + '_waste_percent'"></field-errors>

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
            materials: {
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
    };
</script>
