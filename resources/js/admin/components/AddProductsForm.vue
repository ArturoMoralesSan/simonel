<template>
    <form>
    <!-- Cliente -->
        <div class="form-control mb-4">
            <label for="client_id">Cliente</label>
            <search-select-field
            v-model="fields.client_id"
            name="client_id"
            :options="users"
            placeholder="Selecciona un cliente"
            :initial="sale ? sale.user_id : ''"
            />
            <field-errors name="client_id"></field-errors>
        </div>

        <!-- Tabla de productos -->
        <div class="mb-4">
            <button
            type="button"
            class="btn btn--primary btn--sm mb-2 mt-4"
            @click="addRow"
            >
            + Añadir producto
            </button>

        <table class="table size-caption mx-auto md:table--responsive">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Descuento <span class="description">(%)</span></th>
                    <!-- <th>IVA (%)</th> -->
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(product, index) in fields.products" :key="index">
                    <!-- Selección de producto -->
                    <td>
                        <search-select-field
                        :value="product.id"
                        :options="productsOptions"
                        :name="`products[${index}][product_id]`"
                        placeholder="Selecciona un producto"
                        :initial="sale ? product.product_id : ''"
                        @input="onProductSelected(index, $event)"
                        />
                        <small
                            v-if="errors[`products.${index}.product_id`]"
                            class="text-red-600"
                        >
                            {{ errors[`products.${index}.product_id`][0] }}
                        </small>
                    </td>

                    <!-- Cantidad -->
                    <td>
                        <text-field
                        type="number"
                        class="form-field"
                        v-model.number="product.quantity"
                        :name="`products[${index}][quantity]`"
                        min="1"
                        @input="updateSubtotal(index)"
                        :class="{ 'is-invalid': errors[index]?.quantity }"
                        />
                        <small
                            v-if="errors[`products.${index}.quantity`]"
                            class="text-red-600"
                        >
                            {{ errors[`products.${index}.quantity`][0] }}
                        </small>
                    </td>

                    <!-- Precio unitario -->
                    <td>
                        <text-field
                        type="number"
                        v-model.number="product.unit_price"
                        :name="`products[${index}][unit_price]`"
                        step="0.0001"
                        class="form-field"
                        @input="updateSubtotal(index)"
                        />
                        <small
                            v-if="errors[`products.${index}.unit_price`]"
                            class="text-red-600"
                        >
                            {{ errors[`products.${index}.unit_price`][0] }}
                        </small>
                    </td>

                    <!-- Descuento -->
                    <td>
                        <text-field
                        type="number"
                        v-model.number="product.discount"
                        :name="`products[${index}][discount]`"
                        step="0.0001"
                        class="form-field"
                        min="0"
                        max="100"
                        @input="updateSubtotal(index)"
                        />
                        <small
                            v-if="errors[`products.${index}.discount`]"
                            class="text-red-600"
                        >
                            {{ errors[`products.${index}.discount`][0] }}
                        </small>
                    </td>

                    <!-- IVA -->
                    <!-- <td>
                        <text-field
                        type="number"
                        v-model.number="product.iva"
                        :name="`products[${index}][iva]`"
                        step="0.0001"
                        class="form-field"
                        min="0"
                        max="100"
                        @input="updateSubtotal(index)"
                        />
                    </td> -->

                    <!-- Subtotal -->
                    <td>
                        ${{ calculateSubtotal(product).toFixed(4) }}
                    </td>

                    <!-- Quitar fila -->
                    <td>
                        <button
                            type="button"
                            class="btn btn--danger btn--sm"
                            @click="removeRow(index)"
                            :disabled="index === 0 || fields.products.length === 1"
                        >
                            Quitar
                        </button>
                    </td>
                </tr>
            </tbody>
                <tfoot v-if="fields.products.length">
                    <tr>
                        <td colspan="5" class="text-right font-bold">Subtotal:</td>
                        <td colspan="2">${{ subtotalGeneral.toFixed(4) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right font-bold">Descuentos:</td>
                        <td colspan="2">- ${{ totalDescuentos.toFixed(4) }}</td>
                    </tr>
                    <!-- <tr>
                        <td colspan="5" class="text-right font-bold">IVA:</td>
                        <td colspan="2">+ ${{ totalIva.toFixed(4) }}</td>
                    </tr> -->
                    <tr>
                        <td colspan="5" class="text-right font-bold">Total general:</td>
                        <td colspan="2" class="font-bold">
                            ${{ totalGeneral.toFixed(4) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-control">
            <label for="comment">Comentarios</label>
            <text-area
            name="comment"
            rows="10"
            cols="50"
            v-model="fields.comment"
            maxlength="2000"
            >{{ sale ? sale.comment : '' }}
            </text-area>
            <field-errors name="comment"></field-errors>
        </div>

        <!-- Botón enviar -->
        <div class="text-center pt-4">
            <form-button class="btn--primary btn--wide">
                Enviar
            </form-button>
        </div>
    </form>
</template>

<script>
    import BaseForm from '../../main/components/forms/base/BaseForm.vue';

    export default {
        extends: BaseForm,

        props: {
            products: {
                type: Array,
                required: true
            },
            users: {
                type: Object,
                required: true
            },
            action: {
                type: String,
                required: true
            },
            sale: {
                type: Object,
                default: null
            },
        },

        data() {
            return {
                fields: {
                client_id: null,
                products: [],
                comment: null,
                discounts: null,
                gross_amount: null,
                },
                errors: [],
            };
        },

        created() {
            this.addRow();

            if (this.sale) {

                this.fields.sale_id = this.sale.id;
                this.fields.client_id = this.sale.user_id;
                this.fields.comment = this.sale.comment;

                if (this.sale.products && this.sale.products.length) {

                    this.fields.products = this.sale.products.map(p => ({
                        product_id: Number(p.product_id),
                        quantity: p.quantity || 1,
                        unit_price: p.base_price || 0,
                        discount: p.discount || 0,
                        // iva: p.iva || 16,
                        subtotal: p.subtotal || 0,
                    }));
                }
            }
            
        },

        computed: {

           productsOptions() {
            return this.products.reduce((obj, p) => {

                if (!p.manufactured) {
                    obj[p.id] = 'Sin producto';
                    return obj;
                }

                let label = p.manufactured.name;

                if (p.manufactured.description) {
                    label += ` (${p.manufactured.description})`;
                }

                obj[p.id] = label;

                return obj;

            }, {});
        },
            subtotalGeneral() {

                const total = this.fields.products.reduce((sum, p) => {
                    return sum + (p.quantity * p.unit_price);
                }, 0);

                this.fields.gross_amount = total;

                return total;
            },

            totalDescuentos() {

                const total = this.fields.products.reduce((sum, p) => {

                    const discount =
                    (p.quantity * p.unit_price) *
                    (p.discount / 100);

                    return sum + discount;

                }, 0);

                this.fields.discounts = total;

                return total;
            },

            /* totalIva() {

                return this.fields.products.reduce((sum, p) => {

                    const base =
                    p.quantity * p.unit_price;

                    const discount =
                    base * (p.discount / 100);

                    return sum +
                    ((base - discount) * (p.iva / 100));

                }, 0);
            }, */

            totalGeneral() {
                return (this.subtotalGeneral - this.totalDescuentos); //+  this.totalIva
            }
        },

        methods: {

            addRow() {

                this.fields.products.push({
                    product_id: null,
                    quantity: 1,
                    unit_price: 0,
                    discount: 0,
                    //iva: 16,
                    subtotal: 0
                });

                this.errors.push({});
            },

            removeRow(index) {

                if (index === 0 || this.fields.products.length === 1) {
                    return;
                }

                this.fields.products.splice(index, 1);
                this.errors.splice(index, 1);
            },

            onProductSelected(index, productId) {

                const product = this.fields.products[index];
                product.product_id = productId;
                const selectedProduct =this.products.find(p => p.id == productId);
                if (selectedProduct) {
                    product.unit_price =
                    parseFloat(selectedProduct.costo_venta) || 0;
                } else {

                    product.unit_price = 0;
                }

                this.updateSubtotal(index);
            },

            calculateSubtotal(product) {

                const base = product.quantity * product.unit_price;
                const discount = base * (product.discount / 100);
                return base - discount;
            },

            updateSubtotal(index) {

                const product = this.fields.products[index];

                product.subtotal = this.calculateSubtotal(product);
            },

            validateProducts() {

                let valid = true;

                this.errors = this.fields.products.map(p => {

                    const rowErrors = {};

                    if (!p.product_id) {

                        rowErrors.product_id = 'Debe seleccionar un producto';

                        valid = false;
                    }

                    if (p.quantity <= 0) {

                        rowErrors.quantity = 'Cantidad debe ser mayor a 0';

                        valid = false;
                    }

                    return rowErrors;
                });

                return valid;
            }
        }
    };
</script>

<style scoped>
    .is-invalid {
        border-color: red;
    }
    .text-red-600 {
        color: #dc2626;
        font-size: 0.75rem;
    }
    .table input {
        width: 100%;
    }
</style>
