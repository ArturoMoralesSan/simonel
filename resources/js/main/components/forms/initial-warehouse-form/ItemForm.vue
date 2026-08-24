<template>

    <div class="box box--lg bg-white b-1 rounded relative mb-4">

        <h3>
            Lote {{ index }}
        </h3>

        <button
            v-if="index > minItem"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('remove', index)"
        >
            Eliminar
        </button>


        <!-- TIPO -->

        <div class="row mb-4">

            <div class="md:col sm:col">

                <div class="form-control">

                    <label>
                        Tipo de inventario
                    </label>

                    <select-field
                        :name="'item' + index + '_type'"
                        v-model="fields['item' + index + '_type']"
                        :options="types"
                    />

                    <field-errors
                        :name="'item' + index + '_type'"
                    />

                </div>

            </div>

        </div>


        <!-- MATERIAL / PRODUCTO -->

        <div class="row mb-4">

            <!-- MATERIA PRIMA -->

            <div
                v-if="fields['item' + index + '_type'] === 'raw_material'"
                class="md:col sm:col"
            >

                <div class="form-control">

                    <label>
                        Materia prima
                    </label>

                    <select-field
                        :name="'item' + index + '_raw_material_id'"
                        v-model="fields['item' + index + '_raw_material_id']"
                        :options="materials"
                    />

                    <field-errors
                        :name="'item' + index + '_raw_material_id'"
                    />

                </div>

            </div>


            <!-- PRODUCTO TERMINADO -->

            <div
                v-if="fields['item' + index + '_type'] === 'product'"
                class="md:col sm:col"
            >

                <div class="form-control">

                    <label>
                        Producto terminado
                    </label>

                    <select-field
                        :name="'item' + index + '_product_id'"
                        v-model="fields['item' + index + '_product_id']"
                        :options="products"
                    />

                    <field-errors
                        :name="'item' + index + '_product_id'"
                    />

                </div>

            </div>

        </div>


        <!-- ALMACÉN -->

        <div class="row mb-4">

            <div class="md:col sm:col">

                <div class="form-control">

                    <label>
                        Almacén
                    </label>

                    <select-field
                        :name="'item' + index + '_warehouse_id'"
                        v-model="fields['item' + index + '_warehouse_id']"
                        :options="warehouses"
                    />

                    <field-errors
                        :name="'item' + index + '_warehouse_id'"
                    />

                </div>

            </div>

        </div>


        <!-- CANTIDAD / COSTOS -->

        <div class="row mb-4">

            <!-- CANTIDAD -->

            <div class="md:col-1/3 sm:col">

                <div class="form-control">

                    <label>
                        Cantidad
                    </label>

                    <text-field
                        :name="'item' + index + '_quantity'"
                        v-model="fields['item' + index + '_quantity']"
                        type="number"
                        step="0.01"
                        min="0"
                    />

                    <field-errors
                        :name="'item' + index + '_quantity'"
                    />

                </div>

            </div>


            <!-- COSTO DE FABRICACIÓN -->
            <!-- SOLO PRODUCTO TERMINADO -->

            <div
                v-if="fields['item' + index + '_type'] === 'product'"
                class="md:col-1/3 sm:col"
            >

                <div class="form-control">

                    <label>
                        Costo de fabricación
                    </label>

                    <text-field
                        :name="'item' + index + '_manufacturing_cost'"
                        v-model="fields['item' + index + '_manufacturing_cost']"
                        type="number"
                        step="0.0001"
                        min="0"
                    />

                    <field-errors
                        :name="'item' + index + '_manufacturing_cost'"
                    />

                </div>

            </div>


            <!-- PRECIO -->

            <div
                :class="
                    fields['item' + index + '_type'] === 'product'
                        ? 'md:col-1/3 sm:col'
                        : 'md:col-1/3 sm:col'
                "
            >

                <div class="form-control">

                    <label>

                        {{
                            fields['item' + index + '_type'] === 'raw_material'
                                ? 'Precio unitario'
                                : 'Precio público'
                        }}

                    </label>

                    <text-field
                        :name="'item' + index + '_public_price'"
                        v-model="fields['item' + index + '_public_price']"
                        type="number"
                        step="0.0001"
                        min="0"
                    />

                    <field-errors
                        :name="'item' + index + '_public_price'"
                    />

                </div>

            </div>

        </div>


        <!-- TOTAL -->

        <div class="row mb-4">

            <div class="md:col sm:col">

                <div class="form-control">

                    <label>
                        Total del inventario
                    </label>

                    <div class="inventory-total">

                        $

                        {{
                            (
                                Number(
                                    fields[
                                        'item' + index + '_quantity'
                                    ] || 0
                                )
                                *
                                Number(
                                    fields[
                                        'item' + index + '_public_price'
                                    ] || 0
                                )
                            ).toFixed(2)
                        }}

                    </div>

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

    components: {

        SelectField,

        TextField,

        FieldErrors

    },


    props: {

        index: {

            required: true,

            type: Number

        },


        minItem: {

            required: true,

            type: Number

        },


        materials: {

            required: true,

            type: [Array, Object]

        },


        products: {

            required: true,

            type: [Array, Object]

        },


        warehouses: {

            required: true,

            type: [Array, Object]

        },


        fields: {

            required: true,

            type: Object

        },


        errors: {

            required: true,

            type: Object

        }

    },


    computed: {

        types() {

            return {

                raw_material: 'Materia prima',

                product: 'Producto terminado'

            };

        }

    }

};

</script>


<style scoped>

.inventory-total {

    min-height: 42px;

    display: flex;

    align-items: center;

    padding: 8px 12px;

    background: #f8fafc;

    border: 1px solid #d1d5db;

    border-radius: 4px;

    font-weight: 700;

    font-size: 16px;

}

</style>
