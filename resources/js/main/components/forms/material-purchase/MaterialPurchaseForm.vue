<template>

    <form>

        <div class="row mb-4">

            <div class="col-1/3">

                <div class="form-control">

                    <label>Proveedor</label>

                    <select-field
                        name="supplier_id"
                        v-model="fields.supplier_id"
                        :options="suppliers"
                        :initial="purchaseData.supplier_id ? purchaseData.supplier_id.toString() : ''"
                    >
                    </select-field>

                    <field-errors name="supplier_id"></field-errors>

                </div>

            </div>

            <div class="col-1/3">

                <div class="form-control">

                    <label>Fecha de compra</label>

                    <date-field
                        name="purchase_date"
                        v-model="fields.purchase_date"
                        :initial="formatDate(purchaseData.purchase_date)"
                    >
                    </date-field>

                    <field-errors name="purchase_date"></field-errors>

                </div>

            </div>

            <div class="col-1/3">

                <div class="form-control">

                    <label>Factura</label>

                    <text-field
                        name="invoice_number"
                        v-model="fields.invoice_number"
                        maxlength="100"
                        :initial="purchaseData.invoice_number || ''"
                    >
                    </text-field>

                    <field-errors name="invoice_number"></field-errors>

                </div>

            </div>

        </div>


        <div class="mb-4">

            <ItemForm
                v-for="i in fields.item_count"
                :key="i"
                :index="i"
                :min-item="minItem"
                :materials="materialsData"
                :warehouses="warehouses"
                :assigned-materials="assignedMaterials"
                :errors="errors"
                :fields="fields"
                @removeP="removeItems"
            >
            </ItemForm>

            <p class="pt-4">

                <button
                    v-if="fields.item_count < item"
                    class="btn btn--light mr-4"
                    type="button"
                    @click="fields.item_count++"
                >

                    <img
                        class="mr-1 align-top"
                        :src="$root.path + '/img/svg/plus-circle-primary.svg'"
                        alt=""
                        width="20px"
                    >

                    <span class="align-top">
                        Agregar más elementos
                    </span>

                </button>

                <span v-if="item > 1">
                    Puedes registrar a un máximo de {{ item }} elementos.
                </span>

                <span v-else>
                    Puedes registrar únicamente un elemento.
                </span>

            </p>

        </div>


<div class="box box--lg bg-white b-1 rounded relative mb-8">

    <h3>
        Deshuese del combo
    </h3>

    <div class="row">

        <!-- PIERNA -->

        <div class="md:col-1/2">

            <div class="form-control">

                <label>
                    PIERNA
                </label>

            </div>

            <div class="row mb-2">

                <label>
                    Pulpa <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_pulpa"
                    v-model="fields.deshuese_pulpa"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.pulpa || ''"
                >
                </text-field>

            </div>

            <div class="row mb-2">

                <label>
                    Hueso <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_hueso"
                    v-model="fields.deshuese_hueso"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.hueso || ''"
                >
                </text-field>

            </div>

            <div class="row mb-2">

                <label>
                    Lonja <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_lonja"
                    v-model="fields.deshuese_lonja"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.lonja || ''"
                >
                </text-field>

            </div>

            <div class="row mb-2">

                <label>
                    Cuero planchado <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_cuero_planchado"
                    v-model="fields.deshuese_cuero_planchado"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.cuero_planchar || ''"
                >
                </text-field>

            </div>

            <div class="row mb-2">

                <label>
                    Chamorro <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_chamorro"
                    v-model="fields.deshuese_chamorro"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.chamorro || ''"
                >
                </text-field>

            </div>

        </div>


        <!-- CANAL -->

        <div class="md:col-1/2">

            <div class="form-control">

                <label>
                    CANAL
                </label>

            </div>

            <div class="row mb-2">

                <label>
                    Costilla <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_costilla"
                    v-model="fields.deshuese_costilla"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.costilla || ''"
                >
                </text-field>

            </div>

            <div class="row mb-2">

                <label>
                    Piernas <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_piernas"
                    v-model="fields.deshuese_piernas"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.piernas || ''"
                >
                </text-field>

            </div>

            <div class="row mb-2">

                <label>
                    Paleta <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_paleta"
                    v-model="fields.deshuese_paleta"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.paleta || ''"
                >
                </text-field>

            </div>

            <div class="row mb-2">

                <label>
                    Lomo <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_lomo"
                    v-model="fields.deshuese_lomo"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.lomo || ''"
                >
                </text-field>

            </div>

            <div class="row mb-2">

                <label>
                    Espinazo <span class="description">%</span>
                </label>

                <text-field
                    name="deshuese_espinazo"
                    v-model="fields.deshuese_espinazo"
                    maxlength="6"
                    type="number"
                    step="0.01"
                    :initial="purchaseData.boning?.espinazo || ''"
                >
                </text-field>

            </div>

        </div>

    </div>

</div>




        <!-- COMENTARIOS -->

        <div class="md:row">

            <div class="md:col">

                <div class="form-control">

                    <label for="notes">
                        Comentarios adicionales
                    </label>

                    <text-area
                        name="notes"
                        rows="10"
                        cols="50"
                        v-model="fields.notes"
                        maxlength="2000"
                    >{{ purchaseData.notes || '' }}</text-area>

                    <field-errors name="notes"></field-errors>

                </div>

            </div>

        </div>


        <div class="text-center pt-8">

            <form-button class="btn--primary btn--wide">
                Enviar
            </form-button>

        </div>

    </form>

</template>


<script>

import BaseForm from '../base/BaseForm.vue';
import ItemForm from './ItemForm.vue';

export default {

    extends: BaseForm,

    components: {
        ItemForm
    },

    props: {

        item: {
            required: true,
            type: Number
        },

        minItem: {
            required: true,
            type: Number
        },

        materialsData: {
            required: true,
            type: [Array, Object]
        },

        purchaseData: {
            required: true,
            type: [Array, Object]
        },

        assignedMaterials: {
            required: true,
            type: Array
        },

        suppliers: {
            required: true,
            type: [Array, Object]
        },

        warehouses: {
            required: true,
            type: [Array, Object]
        }

    },

    data() {

        return {

            inputdisabled: true,

            currentDate: null,

            firstTime: null,

            fields: {

                item_count: this.minItem,

                purchase_id: this.purchaseData.id || null,

                deshuese_pulpa:
                    this.purchaseData.deshuese_pulpa || '',

                deshuese_hueso:
                    this.purchaseData.deshuese_hueso || '',

                deshuese_lonja:
                    this.purchaseData.deshuese_lonja || '',

                deshuese_cuero_planchado:
                    this.purchaseData.deshuese_cuero_planchado || '',

                deshuese_chamorro:
                    this.purchaseData.deshuese_chamorro || '',

                deshuese_costilla:
                    this.purchaseData.deshuese_costilla || '',

                deshuese_piernas:
                    this.purchaseData.deshuese_piernas || '',

                deshuese_paleta:
                    this.purchaseData.deshuese_paleta || '',

                deshuese_lomo:
                    this.purchaseData.deshuese_lomo || '',

                deshuese_espinazo:
                    this.purchaseData.deshuese_espinazo || ''

            }

        };

    },

    mounted() {

        if (this.assignedMaterials.length != 0) {

            this.fields.item_count =
                this.assignedMaterials.length ||
                this.minItem;

        }

    },

    watch: {

        firstTime: function(val) {

            this.fields._method =
                val === false
                    ? 'patch'
                    : 'post';

        }

    },

    methods: {

        formatDate(date) {

            if (!date) {

                return '';

            }

            if (date.includes('/')) {

                const [day, month, year] =
                    date.split('/');

                return `${year}-${month}-${day}`;

            }

            return date.substring(0, 10);

        },

        copyItemsFields(source, target) {

            const regex =
                new RegExp(
                    '^item' + source + '_'
                );

            this.deleteItemsFields(target);

            for (let field in this.fields) {

                if (regex.test(field)) {

                    this.$set(
                        this.fields,
                        field.replace(
                            source,
                            target
                        ),
                        this.fields[field]
                    );

                }

            }

        },

        deleteItemsFields(index) {

            const regex =
                new RegExp(
                    '^item' + index + '_'
                );

            for (let field in this.fields) {

                if (regex.test(field)) {

                    delete this.fields[field];

                }

            }

        },

        removeItems(index) {

            for (
                let i = 0;
                i < this.fields.item_count - index;
                i++
            ) {

                this.copyItemsFields(
                    index + i + 1,
                    index + i
                );

            }

            this.fields.item_count--;

            this.deleteItemsFields(
                this.fields.item_count + 1
            );

        }

    }

};

</script>