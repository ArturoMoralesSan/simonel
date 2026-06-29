<template>
    <div class="box box--lg bg-white b-1 rounded relative">
        <h3>
            Producto {{ index }}
        </h3>

        <button v-if="index > minItem"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('removeP', index)"
        >
            Eliminar
        </button>
        <div class="row mb-4">
            <div class="md:col-1/2 sm:col">
                <div class="form-control">
                    <label :for="'item' + index + '-material'">Receta</label>
                    <select-field
                    :name="'item' + index + '_recipes_id'"
                    v-model="fields['item' + index + '_recipes_id']"
                    :options="recipes"
                    :disabled="isAuthorized"
                    :initial="((typeof assignedRecipes[index-1] !== 'undefined') ? assignedRecipes[index-1].product_recipe_id.toString() : '')"
                    >
                    </select-field>
                    <field-errors :name="'item' + index + '_recipes_id'"></field-errors>
                </div>
            </div>
            <div class="md:col-1/2 sm:col">
                <div class="form-control">
                    <label for="quantity">Cantidad a producir</label>
                    <text-field 
                        :name="'item' + index + '_quantity'" 
                        v-model="fields['item' + index + '_quantity']" 
                        maxlength="20" 
                        :disabled="isAuthorized"
                        :initial="((typeof assignedRecipes[index-1] !== 'undefined') ? assignedRecipes[index-1].quantity.toString() : '')"
                    ></text-field>
                    <field-errors :name="'item' + index + '_quantity'"></field-errors>

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
            recipes: {
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
            assignedRecipes: {
                required: true,
                type: Array
            },
            isAuthorized: {
                required: true,
                type: Boolean
            }
        },
        data() {
            return {
                noExistAlert: false,

            };
        },
    };
</script>
