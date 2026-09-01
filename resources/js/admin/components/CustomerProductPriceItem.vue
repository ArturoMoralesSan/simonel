
<template>

    <div class="box box--lg bg-white b-1 rounded relative mb-4">

        <h3>
            Producto {{ index }}
        </h3>

        <button
            v-if="index > 1"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('remove', index)"
        >
            Eliminar
        </button>

        <div class="row mb-4">

            <div class="md:col-1/2">

                <div class="form-control">

                    <label :for="'price' + index + '_product_id'">
                        Producto
                    </label>

                    <select-field
                        :name="'price' + index + '_product_id'"
                        v-model="fields['price' + index + '_product_id']"
                        :options="products"
                        :initial="
                            typeof assignedPrices[index - 1] !== 'undefined'
                                ? String(assignedPrices[index - 1].product_id)
                                : ''
                        "
                    />

                    <field-errors
                        :name="'price' + index + '_product_id'"
                    />

                </div>

            </div>

            <div class="md:col-1/2">

                <div class="form-control">

                    <label :for="'price' + index + '_price'">
                        Precio preferencial
                        <span class="description">$</span>
                    </label>

                    <text-field
                        :name="'price' + index + '_price'"
                        v-model="fields['price' + index + '_price']"
                        type="number"
                        step="0.01"
                        min="0"
                        :initial="
                            typeof assignedPrices[index - 1] !== 'undefined'
                                ? String(assignedPrices[index - 1].price)
                                : ''
                        "
                    />

                    <field-errors
                        :name="'price' + index + '_price'"
                    />

                </div>

            </div>

        </div>

    </div>

</template>


<script>

import SelectField from '../../main/components/forms/base/SelectField.vue';
import TextField from '../../main/components/forms/base/TextField.vue';
import FieldErrors from '../../main/components/forms/base/FieldErrors.vue';

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

        products: {
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
        },

        assignedPrices: {
            required: true,
            type: Array
        }

    }

};

</script>