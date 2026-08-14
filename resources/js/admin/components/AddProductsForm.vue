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

            <table class="table size-caption mx-auto md:table--responsive products-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario <span class="description">($)</span></th>
                    <th>Descuento <span class="description">(%)</span></th>
                    <!-- <th>IVA (%)</th> -->
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(product, index) in fields.products" :key="index">
                    <!-- Selección de producto -->
                    <td data-label="Producto:">
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
                    <td data-label="Cantidad:">
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
                    <td data-label="Precio unitario:">
                        {{ Number(product.unit_price).toFixed(2) }}
                        <text-field
                        type="hidden"
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
                    <td data-label="Descuento:">
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
                    <td data-label="Acciones:">
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
        <div class="mb-4">
            <PaymentForm v-for="i in fields.payments_count" :key="i"
                :index="i"
                :min-payment="minPayment"
                :payments-data="paymentsData"
                :assigned-payments="[]"
                :errors="errors"
                :fields="fields"
                @removeP="removePayments"
            >
            </PaymentForm>

            <p class="pt-4">
                <button v-if="fields.payments_count < payment"
                    class="btn btn--light mr-4"
                    type="button"
                    @click="fields.payments_count++"
                >
                    <img class="mr-1 align-top"
                        :src="$root.path + '/img/svg/plus-circle-primary.svg'"
                        alt=""
                        width="20px"
                    >
                    <span class="align-top">Agregar metodo de pago</span>
                </button>

                <span v-if="payment > 1 "> Puedes registrar a un máximo de {{ payment }} métodos de pago.</span>
                <span v-else> Puedes registrar únicamente un método de pago.</span>
            </p>
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
    import PaymentForm from '../../main/components/forms/order-status-form/PaymentForm.vue';


    export default {
        extends: BaseForm,

        components: { PaymentForm },

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
            payment: {
                required: true,
                type: Number
            },
            minPayment: {
                required: true,
                type: Number
            },
            paymentsData: {
                required: true,
                type: [Array, Object]
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
                payments_count: this.minPayment,
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
                this.$nextTick(() => {
                    this.updateFirstPayment();
                });
            },

            updateFirstPayment() {

                if (this.fields.payment1_cost === undefined) {
                    return;
                }

                this.fields.payment1_cost = this.totalGeneral.toFixed(2);
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

                this.$nextTick(() => {
                    this.updateFirstPayment();
                });
            },

            calculateSubtotal(product) {

                const base = product.quantity * product.unit_price;
                const discount = base * (product.discount / 100);
                return base - discount;
            },

            updateSubtotal(index) {

                const product = this.fields.products[index];

                product.subtotal = this.calculateSubtotal(product);

                this.$nextTick(() => {
                    this.updateFirstPayment();
                });
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
            },

            /**
             * Copy all author's fields from one card to another.
             *
             * @param {Integer} source
             * @param {Integer} target
             */
             copyPaymentFields(source, target) {
                const regex = new RegExp('^payment' + source + '_');

                this.deleteAuthorFields(target);

                for (let field in this.fields) {
                    if (regex.test(field)) {
                        this.$set(this.fields, field.replace(source, target), this.fields[field]);
                    }
                }
            },

            /**
             * Delete all fields for the given author.
             *
             * @param {Integer} index
             */
             deletePaymentFields(index) {
                const regex = new RegExp('^payment' + index + '_');

                for (let field in this.fields) {
                    if (regex.test(field)) {
                        delete this.fields[field];
                    }
                }
            },

            /**
             * Copy all necessary author fields to move their index
             * and then remove the last card.
             *
             * @param {Integer} index
             */
             removePayments(index) {
                for (let i = 0; i < this.fields.payments_count - index; i ++) {
                    this.copyPaymentFields(index + i + 1, index + i);
                }

                this.fields.payments_count--;

                this.deletePaymentFields(this.fields.payments_count + 1);
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

/* ==========================================
   RESPONSIVE DE LA TABLA DE PRODUCTOS
   Solo afecta .products-table
   ========================================== */

@media (max-width: 767px) {

    /* Ocultar encabezados */
    .products-table thead {
        display: none;
    }

    /* Cada producto como tarjeta */
    .products-table tbody tr {
        display: block;
        margin-bottom: 16px;
        padding: 12px 14px;
        border: 1px solid #e1e5e9;
        border-radius: 8px;
        background: #fff;
    }

    /* Celdas */
    .products-table tbody td {
        display: flex;
        flex-direction: column;
        width: 100%;
        box-sizing: border-box;

        padding: 10px 0;
        border: 0;
        border-bottom: 1px solid #eee;
    }

    .products-table tbody td:last-child {
        border-bottom: 0;
    }

    /* Etiquetas usando data-label */
    .products-table tbody td[data-label]::before {
        content: attr(data-label);

        margin-bottom: 6px;

        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }

    /* Inputs */
    .products-table tbody td input {
        width: 100%;
        box-sizing: border-box;
    }

    /* Precio unitario */
    .products-table tbody td[data-label="Precio unitario:"] {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    .products-table tbody td[data-label="Precio unitario:"]::before {
        margin-bottom: 0;
    }

    /* Subtotal */
    .products-table tbody td[data-label="Subtotal:"] {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    .products-table tbody td[data-label="Subtotal:"]::before {
        margin-bottom: 0;
    }

    /* Botón quitar */
    .products-table tbody td[data-label="Acciones:"] button {
        width: 100%;
    }

    /* ==========================================
       RESUMEN
       ========================================== */

    .products-table tfoot {
        display: block;
        margin-top: 16px;
    }

    .products-table tfoot tr {
        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 12px 14px;

        border-bottom: 1px solid #e5e7eb;
        background: #fff;
    }

    .products-table tfoot td {
        display: block;
        width: auto !important;
        padding: 0;
        border: 0;
    }

    .products-table tfoot td:first-child {
        text-align: left !important;
    }

    .products-table tfoot td:last-child {
        text-align: right !important;
    }

    /* Total general */
    .products-table tfoot tr:last-child {
        padding-top: 15px;
        padding-bottom: 15px;
        background: #f8f9fa;
        font-size: 16px;
    }
}
</style>
