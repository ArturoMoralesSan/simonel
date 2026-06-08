<template>
    <div class="box box--lg bg-white b-1 rounded relative">
        <h3>
            {{ fields['product' + index + '_product_name'] ? fields['product' + index + '_product_name'] : 'Producto ' + index }}
            <span v-if="fields['product' + index + '_product_name']" class="badge">
                Producto {{ index }}
            </span>
        </h3>
        <div v-if="index > minProduct" class="form-control clone-select-wrapper">
            <label>Clonar de:</label>
            <select v-model="selectedCloneIndex" @change="handleClone">
                <option value="" disabled>Selecciona un producto</option>
                <option v-for="(indx, idx) in this.fields.products_count" 
                        :key="indx" 
                        :value="indx"
                        v-if="indx !== index"> 
                    Producto {{ idx + 1 }}
                </option>
            </select>
        </div>

        <button v-if="index > minProduct"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('remove', index)"
        >
            Eliminar
        </button>
        <div class="md:row mb-4">
            <div class="md:col">
                <div class="form-control">
                    <label :for="'product' + index + '-product_name'">Nombre del producto</label>
                    <text-field :name="'product' + index + '_product_name'" v-model="fields['product' + index + '_product_name']" maxlength="80" 
                    :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].pivot.product_name : '')">
                    </text-field>
                    <field-errors :name="'product' + index + '_product_name'"></field-errors>
                </div>
            </div>
        </div>
        <div class="md:row mb-4">
            <div class="md:col-1/3">
                <div class="form-control">
                    <label :for="'product' + index + '-type_id'">Categoría</label>
                    <select-field
                    :name="'product' + index + '_type_id'"
                    v-model="fields['product' + index + '_type_id']"
                    :options="types"
                    :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].type_id.toString() : '')"
                    >
                    </select-field>
                    <field-errors :name="'product' + index + '_type_id'"></field-errors>
                </div>
            </div>
            <div class="md:col-1/3">
                <div class="form-control">
                    <label :for="'product' + index + '-product_id'">Producto</label>
                    <search-select-field
                        :ref="'searchSelect' + index"
                        :name="'product' + index + '_product_id'"
                        v-model="fields['product' + index + '_product_id']"
                        :options="filteredProducts"
                        :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].id.toString() : '')"
                    >
                    </search-select-field>
                    <field-errors :name="'product' + index + '_product_id'"></field-errors>
                </div>
            </div>
            <div class="md:col-1/3">
                <div class="form-control">
                    <label :for="'product' + index + '-cut_id'">Acabado</label>
                    <select-field
                    :name="'product' + index + '_cut_id'"
                    v-model="fields['product' + index + '_cut_id']"
                    :options="formattedCuts"
                    :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].pivot.cut_id.toString() : '')"
                    >
                    </select-field>
                    <field-errors :name="'product' + index + '_cut_id'"></field-errors>
                </div>
            </div>
        </div>
        <div class="md:row mb-2">
            <div class="md:col-1/3">
                <div class="form-control">
                    <label for="width">Ancho</label>
                    <text-field :name="'product' + index + '_width'" v-model="fields['product' + index + '_width']" maxlength="80"
                    :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].pivot.width : '')">
                    </text-field>
                    <field-errors :name="'product' + index + '_width'"></field-errors>
                </div>
            </div>
            <div class="md:col-1/3">
                <div class="form-control">
                    <label for="height">Largo</label>
                    <text-field :name="'product' + index + '_height'" v-model="fields['product' + index + '_height']" maxlength="80"
                    :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].pivot.height : '')">
                    </text-field>
                    <field-errors :name="'product' + index + '_height'"></field-errors>
                </div>
            </div>
            <div class="md:col-1/3">
                <div class="form-control">
                    <label for="quantity_product">Cantidad de material utilizado</label>
                    <text-field :name="'product' + index + '_quantity_product'" v-model="fields['product' + index + '_quantity_product']" maxlength="80"
                    :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].pivot.quantity_product.toString() : '')">
                    </text-field>
                    <field-errors :name="'product' + index + '_quantity_product'"></field-errors>
                </div>
            </div>
        </div>
        <div class="md:row mb-2">
            <div class="md:col-1/2">
                <div class="form-control">
                    <label for="base_price">Costo por unidad</label>
                    <text-field disabled :name="'product' + index + '_base_price'" v-model="fields['product' + index + '_base_price']" maxlength="80"
                    :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].pivot.base_price : '')">
                    </text-field>
                    <field-errors :name="'product' + index + '_base_price'"></field-errors>
                </div>
            </div>
            <div class="md:col-1/2">
                <div class="form-control">
                    <label for="profit_percentage">Utilidad por unidad</label>
                    <text-field :name="'product' + index + '_profit_percentage'" v-model="fields['product' + index + '_profit_percentage']" maxlength="6" 
                    :initial="((typeof assignedProducts[index-1] !== 'undefined') ? assignedProducts[index-1].pivot.profit_percentage : '')">
                    </text-field>
                    <field-errors :name="'product' + index + '_profit_percentage'"></field-errors>
                </div>
            </div>
        </div>
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
</template>

<script>
    import SelectField from '../base/SelectField.vue';
    import SearchSelectField from '../base/SearchSelectField.vue';
    import TextField from '../base/TextField.vue';
    import FieldErrors from '../base/FieldErrors.vue';

    export default {
        components: { SelectField,SearchSelectField,  FieldErrors, TextField },

        props: {
            products: {
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
            
            minProduct: {
                required: true,
                type: Number
            },
            assignedProducts: {
                required: true,
                type: Array
            },
            cuts: {
                required: true,
                type: [Array, Object]
            },
            types: {
                required: true,
                type: [Array, Object]
            },
        },
        data() {
            return {
                noExistAlert: false,
                formattedProducts: {},
                formattedCuts: {},
                selectedCloneIndex: "",

            };
        },
        created() {
            this.formatProducts();
            this.formatCuts();
        },
        mounted() {
            this.$watch(
                () => this.fields['product' + this.index + '_type_id'],
                (newType, oldType) => {
                    if (this.$parent.cloning) {
                        // Si estamos clonando, asegurarnos de que el nuevo producto es tratado como nuevo
                        console.log("🛠 Clonando producto en edición");
                    } else {
                        // Verificar si el producto ya existe en assignedProducts
                        const assignedProduct = this.assignedProducts[this.index - 1];

                        if (assignedProduct && assignedProduct.id) {
                            console.log("✋ Producto existente en edición, no se reinicia");
                            return; // ❌ No resetear si el producto ya viene de la base de datos
                        }
                    }
                    if (newType && newType !== oldType) {
                        this.$set(this.fields, 'product' + this.index + '_product_id', null);
                        this.$set(this.fields, 'product' + this.index + '_base_price', 0);

                        this.$nextTick(() => {
                            if (this.$refs['searchSelect' + this.index]) {
                                this.$refs['searchSelect' + this.index].clearField();
                            }
                        });
                    }
                },
                { immediate: true }
            );



            this.$watch(
                'fields',
                () => {
                    this.updateBasePrice();
                },
                { deep: true }
            );
        },



        computed: {
            filteredProducts() {
                if (!this.fields['product' + this.index + '_type_id']) {
                    return {};
                }

                let filtered = {};
                this.products.forEach(product => {
                    if (product.type_id == this.fields['product' + this.index + '_type_id']) {
                        filtered[product.id] = product.name;
                    }
                });
                return filtered;
            },

            salePrice() {
                const basePrice = parseFloat(this.fields['product' + this.index + '_base_price']) || 0;
                const profitPercentage = parseFloat(this.fields['product' + this.index + '_profit_percentage']) || 0;
                const priceWithProfit = basePrice + (basePrice * profitPercentage / 100);
                
                return priceWithProfit;
            }
        },

        methods: {
            formatProducts() {
                this.formattedProducts = this.products.reduce((obj, product) => {
                    obj[product.id] = product.name;
                    return obj;
                }, {});
            },
            formatCuts() {
                this.formattedCuts = this.cuts.reduce((obj, cut) => {
                    obj[cut.id] = cut.name;
                    return obj;
                }, {});
            },
            updateBasePrice() {
                const productId = this.fields['product' + this.index + '_product_id'];
                const cutId = this.fields['product' + this.index + '_cut_id'];
                const quantity = parseFloat(this.fields['product' + this.index + '_quantity_product']) || 1; // Por defecto 1

                let basePrice = 0;

                // Obtener el producto seleccionado
                const selectedProduct = this.products.find(product => product.id == productId);
                if (selectedProduct) {
                    basePrice = (parseFloat(selectedProduct.costo_total) || 0) * quantity;
                }

                // Obtener el corte seleccionado y sumarlo
                const selectedCut = this.cuts.find(cut => cut.id == cutId);
                if (selectedCut) {
                    basePrice += (parseFloat(selectedCut.cost) || 0)  * quantity;
                }

                // Asignar el nuevo precio base
                this.$set(this.fields, 'product' + this.index + '_base_price', basePrice);
            },

             
            handleClone() {
                if (this.selectedCloneIndex !== "") {
                    this.$emit("cloneProduct", this.selectedCloneIndex, this.index);
                }
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
        color: #2c7a7b; /* Verde azulado */
    }

    .clone-select-wrapper {
        position: absolute;
        top: 0;
        right: 85px;
        background: #436eb3;
        color: #fff;
        padding: 0 5px;
    }
    .clone-select-wrapper label {
        font-size: 11px;
    }
    .clone-select-wrapper select {
        font-size: 11px;
    }
    .badge {
    background-color: #436eb3;
    color: white;
    font-size: 12px;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 12px;
}

</style>

