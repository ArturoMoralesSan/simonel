<template>
    <form>
        <div class="mb-4">
            <div class="box box--lg bg-white b-1 rounded relative">
                <!-- Nombre -->
                <div class="md:row mb-4">
                    <div class="md:col">
                        <div class="form-control">
                            <label for="product_name">Nombre del producto</label>
                            <text-field name="product_name" v-model="fields.product_name" maxlength="80" 
                            :initial="template ? template.product_name : ''"/>
                            <field-errors name="product_name"></field-errors>
                        </div>
                    </div>
                </div>

                <!-- Categoría, Producto, Acabado -->
                <div class="md:row mb-4">
                    <div class="md:col-1/3">
                        <div class="form-control">
                            <label for="type_id">Categoría</label>
                            <select-field
                                name="type_id"
                                v-model="fields.type_id"
                                :options="types"
                                :initial="template && template.product ? template.product.type_id : ''"                                
                            />
                            <field-errors name="type_id"></field-errors>
                        </div>
                    </div>
                    <div class="md:col-1/3">
                        <div class="form-control">
                            <label for="product_id">Producto</label>
                            <search-select-field
                                ref="searchSelect"
                                name="product_id"
                                v-model="fields.product_id"
                                :options="filteredProducts"
                                :initial="template ? template.product_id : ''"
                            />
                            <field-errors name="product_id"></field-errors>
                        </div>
                    </div>
                    <div class="md:col-1/3">
                        <div class="form-control">
                            <label for="cut_id">Acabado</label>
                            <select-field
                                name="cut_id"
                                v-model="fields.cut_id"
                                :options="formattedCuts"
                                :initial="template ? template.cut_id : ''"

                            />
                            <field-errors name="cut_id"></field-errors>
                        </div>
                    </div>
                </div>

                <!-- Dimensiones -->
                <div class="md:row mb-2">
                    <div class="md:col-1/3">
                        <div class="form-control">
                            <label for="width">Ancho</label>
                            <text-field name="width" v-model="fields.width" maxlength="80" 
                            :initial="template ? template.width : ''"/>
                            <field-errors name="width"></field-errors>
                        </div>
                    </div>
                    <div class="md:col-1/3">
                        <div class="form-control">
                            <label for="height">Largo</label>
                            <text-field name="height" v-model="fields.height" maxlength="80" 
                            :initial="template ? template.height : ''"/>
                            <field-errors name="height"></field-errors>
                        </div>
                    </div>
                    <div class="md:col-1/3">
                        <div class="form-control">
                            <label for="quantity_product">Cantidad de material utilizado</label>
                            <text-field name="quantity_product" v-model="fields.quantity_product" maxlength="80" 
                            :initial="template ? template.quantity_product : ''"/>
                            <field-errors name="quantity_product"></field-errors>
                        </div>
                    </div>
                </div>

                <!-- Precios -->
                <div class="md:row mb-2">
                    <div class="md:col-1/2">
                        <div class="form-control">
                            <label for="base_price">Costo por unidad</label>
                            <text-field disabled name="base_price" v-model="fields.base_price" maxlength="80" 
                            :initial="template ? template.base_price : ''"/>
                            <field-errors name="base_price"></field-errors>
                        </div>
                    </div>
                    <div class="md:col-1/2">
                        <div class="form-control">
                            <label for="profit_percentage">Utilidad por unidad (%)</label>
                            <text-field name="profit_percentage" v-model="fields.profit_percentage" maxlength="6" 
                            :initial="template ? template.profit_percentage : ''"/>
                            <field-errors name="profit_percentage"></field-errors>
                        </div>
                    </div>
                </div>
                <div class="md:row mb-4">
                    <div class="md:col-1/2">
                        <div class="form-control">
                            <label for="has_inventory">¿Deseas generar inventario?</label>
                            <select-field
                                name="has_inventory"
                                v-model="fields.has_inventory"
                                :options="{ 1: 'Sí', 0: 'No' }"
                                :initial="template ? template.has_inventory : 0"
                            />
                            <field-errors name="has_inventory"></field-errors>
                        </div>
                    </div>
                </div>

                <!-- Importe -->
                <div class="md:row mb-2">
                    <div class="md:col">
                        <div class="sale-price-wrapper">
                            <div class="sale-price-container">
                                <label class="sale-price-label">Importe</label>
                                <p class="sale-price-value">${{ salePrice.toFixed(4) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón -->
        <div class="text-center pt-8">
            <form-button class="btn--primary btn--wide">
                Enviar
            </form-button>
        </div>
   </form>
</template>

<script>
import { template } from 'lodash';
import BaseForm from '../base/BaseForm.vue';

export default {
    extends: BaseForm,
    

    props: {
        template: { 
            type: [Object, Array], 
            required: false 
        },
        products: { 
            type: [Object, Array], 
            required: true 
        },
        cuts: { 
            type: [Array, Object], 
            required: true 
        },
        types: { 
            type: [Array, Object], 
            required: true 
        },
        mode: { 
            type: [String], 
            required: true 
        }
    },

    data() {
        return {
            fields: {
                product_name: this.template ? this.template.product_name : '',
                type_id: this.template ? this.template.type_id : '',
                product_id: this.template ? this.template.product_id : '',
                cut_id: this.template ? this.template.cut_id : '',
                width: this.template ? this.template.width : '',
                height: this.template ? this.template.height : '',
                quantity_product: this.template ? this.template.quantity_product : '',
                base_price: this.template ? this.template.base_price : 0,
                profit_percentage: this.template ? this.template.profit_percentage : '',
                mode: this.mode,
                has_inventory: this.template ? this.template.has_inventory : 0,
            },
            formattedCuts: {},
        };
    },

    computed: {
        filteredProducts() {
            if (!this.fields.type_id) return {};
            return this.products
                .filter(p => p.type_id == this.fields.type_id)
                .reduce((obj, p) => {
                    obj[p.id] = p.name;
                    return obj;
                }, {});
        },

        salePrice() {
            const base = parseFloat(this.fields.base_price) || 0;
            const profit = parseFloat(this.fields.profit_percentage) || 0;
            return base + (base * profit / 100);
        }
    },

    watch: {
        fields: {
            handler() {
                this.updateBasePrice();
            },
            deep: true
        }
    },

    created() {
        this.formatCuts();
    },

    methods: {
        formatCuts() {
            this.formattedCuts = this.cuts.reduce((obj, cut) => {
                obj[cut.id] = cut.name;
                return obj;
            }, {});
        },

        updateBasePrice() {
            const productId = this.fields.product_id;
            const cutId = this.fields.cut_id;
            const quantity = parseFloat(this.fields.quantity_product) || 1;

            let basePrice = 0;

            const selectedProduct = this.products.find(p => p.id == productId);
            if (selectedProduct) {
                basePrice = (parseFloat(selectedProduct.costo_total) || 0) * quantity;
            }

            const selectedCut = this.cuts.find(c => c.id == cutId);
            if (selectedCut) {
                basePrice += (parseFloat(selectedCut.cost) || 0) * quantity;
            }

            this.fields.base_price = basePrice;
        }
    }
};
</script>

<style scoped>
    .sale-price-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 1rem;
    }
    .sale-price-container {
        text-align: center; 
        padding: 1.5rem;
    }
    .sale-price-label {
        display: block;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .sale-price-value {
        font-size: 24px;
        font-weight: bold;
        color: #2c7a7b;
    }
</style>
