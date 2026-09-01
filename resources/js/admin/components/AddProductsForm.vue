<template>
    <form>

        <!-- Cliente -->
        <div class="form-control mb-4">
            <label for="client_id">
                Cliente
            </label>

            <search-select-field
                v-model="fields.client_id"
                name="client_id"
                :options="usersOptions"
                placeholder="Selecciona un cliente"
                :initial="sale ? String(sale.user_id) : ''"
                @input="onClientSelected"
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


            <table
                class="table size-caption mx-auto md:table--responsive products-table"
            >

                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>
                            Precio al público
                            <span class="description">($)</span>
                        </th>
                        <th>
                            Descuento
                            <span class="description">(%)</span>
                        </th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>


                <tbody>

                    <tr
                        v-for="(product, index) in fields.products"
                        :key="index"
                    >

                        <!-- Producto -->
                        <td data-label="Producto:">

                            <search-select-field
                                :value="product.product_id"
                                :options="productsOptions"
                                :name="`products[${index}][product_id]`"
                                placeholder="Selecciona un producto"
                                :initial="
                                    sale
                                        ? String(product.product_id)
                                        : ''
                                "
                                @input="
                                    onProductSelected(
                                        index,
                                        $event
                                    )
                                "
                            />

                            <small
                                v-if="
                                    errors[
                                        `products.${index}.product_id`
                                    ]
                                "
                                class="text-red-600"
                            >
                                {{
                                    errors[
                                        `products.${index}.product_id`
                                    ][0]
                                }}
                            </small>

                        </td>


                        <!-- Cantidad -->
                        <td data-label="Cantidad:">

                            <text-field
                                type="number"
                                class="form-field"
                                v-model.number="product.quantity"
                                :name="
                                    `products[${index}][quantity]`
                                "
                                min="1"
                                @input="
                                    updateSubtotal(index)
                                "
                            />

                            <small
                                v-if="
                                    errors[
                                        `products.${index}.quantity`
                                    ]
                                "
                                class="text-red-600"
                            >
                                {{
                                    errors[
                                        `products.${index}.quantity`
                                    ][0]
                                }}
                            </small>

                        </td>


                        <!-- Precio unitario -->
                        <td data-label="Precio unitario:">

                            {{
                                Number(
                                    product.unit_price
                                ).toFixed(2)
                            }}

                            <text-field
                                type="hidden"
                                v-model.number="
                                    product.unit_price
                                "
                                :name="
                                    `products[${index}][unit_price]`
                                "
                                step="0.0001"
                                class="form-field"
                            />

                            <small
                                v-if="
                                    errors[
                                        `products.${index}.unit_price`
                                    ]
                                "
                                class="text-red-600"
                            >
                                {{
                                    errors[
                                        `products.${index}.unit_price`
                                    ][0]
                                }}
                            </small>

                        </td>


                        <!-- Descuento -->
                        <td data-label="Descuento:">

                            <text-field
                                type="number"
                                v-model.number="
                                    product.discount
                                "
                                :name="
                                    `products[${index}][discount]`
                                "
                                step="0.0001"
                                class="form-field"
                                min="0"
                                max="100"
                                @input="
                                    updateSubtotal(index)
                                "
                            />

                            <small
                                v-if="
                                    errors[
                                        `products.${index}.discount`
                                    ]
                                "
                                class="text-red-600"
                            >
                                {{
                                    errors[
                                        `products.${index}.discount`
                                    ][0]
                                }}
                            </small>

                        </td>


                        <!-- Subtotal -->
                        <td data-label="Subtotal:">

                            ${{
                                calculateSubtotal(product)
                                    .toFixed(2)
                            }}

                        </td>


                        <!-- Quitar -->
                        <td data-label="Acciones:">

                            <button
                                type="button"
                                class="btn btn--danger btn--sm"
                                @click="removeRow(index)"
                                :disabled="
                                    index === 0 ||
                                    fields.products.length === 1
                                "
                            >
                                Quitar
                            </button>

                        </td>

                    </tr>

                </tbody>


                <!-- Resumen -->
                <tfoot v-if="fields.products.length">

                    <tr>

                        <td
                            colspan="5"
                            class="text-right font-bold"
                        >
                            Subtotal:
                        </td>

                        <td colspan="2">
                            ${{
                                subtotalGeneral.toFixed(2)
                            }}
                        </td>

                    </tr>


                    <tr>

                        <td
                            colspan="5"
                            class="text-right font-bold"
                        >
                            Descuentos:
                        </td>

                        <td colspan="2">
                            - ${{
                                totalDescuentos.toFixed(2)
                            }}
                        </td>

                    </tr>


                    <tr>

                        <td
                            colspan="5"
                            class="text-right font-bold"
                        >
                            Total general:
                        </td>

                        <td
                            colspan="2"
                            class="font-bold"
                        >
                            ${{
                                totalGeneral.toFixed(2)
                            }}
                        </td>

                    </tr>

                </tfoot>

            </table>

        </div>


        <!-- Métodos de pago -->
        <div class="mb-4">

            <PaymentForm
                v-for="i in fields.payments_count"
                :key="i"
                :index="i"
                :min-payment="minPayment"
                :payments-data="paymentsData"
                :assigned-payments="[]"
                :errors="errors"
                :fields="fields"
                @removeP="removePayments"
            />

            <p class="pt-4">

                <button
                    v-if="
                        fields.payments_count < payment
                    "
                    class="btn btn--light mr-4"
                    type="button"
                    @click="
                        fields.payments_count++
                    "
                >

                    <img
                        class="mr-1 align-top"
                        :src="
                            $root.path +
                            '/img/svg/plus-circle-primary.svg'
                        "
                        alt=""
                        width="20px"
                    >

                    <span class="align-top">
                        Agregar metodo de pago
                    </span>

                </button>


                <span v-if="payment > 1">
                    Puedes registrar a un máximo de
                    {{ payment }}
                    métodos de pago.
                </span>

                <span v-else>
                    Puedes registrar únicamente un método
                    de pago.
                </span>

            </p>

        </div>


        <!-- Comentarios -->
        <div class="form-control">

            <label for="comment">
                Comentarios
            </label>

            <text-area
                name="comment"
                rows="10"
                cols="50"
                v-model="fields.comment"
                maxlength="2000"
            >{{ sale ? sale.comment : '' }}</text-area>

            <field-errors name="comment"></field-errors>

        </div>


        <!-- Enviar -->
        <div class="text-center pt-4">

            <form-button class="btn--primary btn--wide">
                Enviar
            </form-button>

        </div>

    </form>
</template>


<script>

import BaseForm
    from '../../main/components/forms/base/BaseForm.vue';

import PaymentForm
    from '../../main/components/forms/order-status-form/PaymentForm.vue';


export default {

    extends: BaseForm,

    components: {
        PaymentForm
    },


    props: {

        products: {
            type: Array,
            required: true
        },

        /*
         * users contiene:
         *
         * {
         *     15: {
         *         id: 15,
         *         name: 'Cliente A (Empresa A)',
         *         product_prices: [
         *             {
         *                 product_id: 5,
         *                 price: 120
         *             }
         *         ]
         *     }
         * }
         */
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
        }

    },


    data() {

        return {

            fields: {

                client_id: null,

                products: [],

                comment: null,

                discounts: null,

                gross_amount: null,

                payments_count: this.minPayment

            },

            errors: []

        };

    },


    created() {

        /*
         * Crear primera fila
         */
        this.addRow();


        /*
         * Si estamos editando una venta
         */
        if (this.sale) {

            this.fields.sale_id =
                this.sale.id;

            this.fields.client_id =
                this.sale.user_id;

            this.fields.comment =
                this.sale.comment;


            /*
             * Cargar productos existentes
             */
            if (
                this.sale.products &&
                this.sale.products.length
            ) {

                this.fields.products =
                    this.sale.products.map(p => ({

                        product_id:
                            Number(p.product_id),

                        quantity:
                            p.quantity || 1,

                        unit_price:
                            p.base_price || 0,

                        discount:
                            p.discount || 0,

                        subtotal:
                            p.subtotal || 0

                    }));

            }

        }

    },


    computed: {

        /*
         * =====================================================
         * CLIENTES PARA SEARCH-SELECT
         * =====================================================
         *
         * Convierte:
         *
         * users = {
         *     15: {
         *         name: 'Cliente A'
         *     },
         *     20: {
         *         name: 'Cliente B'
         *     }
         * }
         *
         * en:
         *
         * {
         *     15: 'Cliente A',
         *     20: 'Cliente B'
         * }
         *
         * Es equivalente al pluck().
         */
        usersOptions() {

            return Object.keys(this.users)
                .reduce((options, userId) => {

                    options[userId] =
                        this.users[userId].name;

                    return options;

                }, {});

        },


        /*
         * Productos para search-select
         */
        productsOptions() {

            return this.products.reduce(
                (obj, p) => {

                    if (!p.manufactured) {

                        obj[p.id] =
                            'Sin producto';

                        return obj;

                    }


                    let label =
                        p.manufactured.name;


                    if (
                        p.manufactured.description
                    ) {

                        label +=
                            ` (${p.manufactured.description})`;

                    }


                    obj[p.id] = label;

                    return obj;

                },
                {}
            );

        },


        /*
         * Cliente seleccionado
         */
        selectedClient() {

            if (!this.fields.client_id) {
                return null;
            }

            return (
                this.users[this.fields.client_id] ||
                null
            );

        },


        /*
         * Subtotal general
         */
        subtotalGeneral() {

            const total =
                this.fields.products.reduce(
                    (sum, p) => {

                        return sum +
                            (
                                Number(
                                    p.quantity || 0
                                ) *
                                Number(
                                    p.unit_price || 0
                                )
                            );

                    },
                    0
                );


            this.fields.gross_amount =
                total;


            return total;

        },


        /*
         * Descuentos
         */
        totalDescuentos() {

            const total =
                this.fields.products.reduce(
                    (sum, p) => {

                        const base =
                            Number(
                                p.quantity || 0
                            ) *
                            Number(
                                p.unit_price || 0
                            );


                        const discount =
                            base *
                            (
                                Number(
                                    p.discount || 0
                                ) / 100
                            );


                        return sum + discount;

                    },
                    0
                );


            this.fields.discounts =
                total;


            return total;

        },


        /*
         * Total
         */
        totalGeneral() {

            return (
                this.subtotalGeneral -
                this.totalDescuentos
            );

        }

    },


    methods: {

        /*
         * =====================================================
         * CAMBIAR CLIENTE
         * =====================================================
         *
         * Al cambiar el cliente se revisan nuevamente todos
         * los productos que ya estén seleccionados.
         */
        onClientSelected(clientId) {

            this.fields.client_id =
                clientId;


            this.fields.products.forEach(
                (product, index) => {

                    if (product.product_id) {

                        this.onProductSelected(
                            index,
                            product.product_id
                        );

                    }

                }
            );

        },


        /*
         * =====================================================
         * AGREGAR PRODUCTO
         * =====================================================
         */
        addRow() {

            this.fields.products.push({

                product_id: null,

                quantity: 1,

                unit_price: 0,

                discount: 0,

                subtotal: 0

            });


            this.errors.push({});

        },


        /*
         * =====================================================
         * ELIMINAR PRODUCTO
         * =====================================================
         */
        removeRow(index) {

            if (
                index === 0 ||
                this.fields.products.length === 1
            ) {

                return;

            }


            this.fields.products.splice(
                index,
                1
            );

            this.errors.splice(
                index,
                1
            );


            this.$nextTick(() => {

                this.updateFirstPayment();

            });

        },


        /*
         * =====================================================
         * SELECCIONAR PRODUCTO
         * =====================================================
         */
        onProductSelected(
            index,
            productId
        ) {

            const product =
                this.fields.products[index];


            product.product_id =
                productId;


            /*
             * Buscar producto
             */
            const selectedProduct =
                this.products.find(
                    p =>
                        p.id == productId
                );


            /*
             * Si no existe
             */
            if (!selectedProduct) {

                product.unit_price =
                    0;

                this.updateSubtotal(
                    index
                );

                return;

            }


            /*
             * =================================================
             * PRECIO NORMAL
             * =================================================
             *
             * Si no existe precio preferencial,
             * utilizamos costo_venta.
             */
            let price =
                parseFloat(
                    selectedProduct.costo_venta
                ) || 0;


            /*
             * =================================================
             * BUSCAR CLIENTE
             * =================================================
             */
            const client =
                this.users[
                    this.fields.client_id
                ];


            /*
             * =================================================
             * BUSCAR PRECIO PREFERENCIAL
             * =================================================
             */
            if (
                client &&
                Array.isArray(
                    client.product_prices
                )
            ) {

                const preferredPrice =
                    client.product_prices.find(
                        item =>
                            Number(
                                item.product_id
                            ) ===
                            Number(
                                productId
                            )
                    );


                /*
                 * Existe precio preferencial
                 */
                if (preferredPrice) {

                    const preferred =
                        parseFloat(
                            preferredPrice.price
                        );


                    /*
                     * Solo sustituimos si el precio
                     * es válido.
                     */
                    if (
                        !isNaN(preferred)
                    ) {

                        price =
                            preferred;

                    }

                }

            }


            /*
             * Precio final
             *
             * Preferencial si existe.
             * Normal si no existe.
             */
            product.unit_price =
                price;


            /*
             * Actualizar subtotal
             */
            this.updateSubtotal(
                index
            );


            /*
             * Actualizar pago
             */
            this.$nextTick(() => {

                this.updateFirstPayment();

            });

        },


        /*
         * =====================================================
         * CALCULAR SUBTOTAL
         * =====================================================
         */
        calculateSubtotal(product) {

            const base =
                Number(
                    product.quantity || 0
                ) *
                Number(
                    product.unit_price || 0
                );


            const discount =
                base *
                (
                    Number(
                        product.discount || 0
                    ) / 100
                );


            return base - discount;

        },


        /*
         * =====================================================
         * ACTUALIZAR SUBTOTAL
         * =====================================================
         */
        updateSubtotal(index) {

            const product =
                this.fields.products[index];


            product.subtotal =
                this.calculateSubtotal(
                    product
                );


            this.$nextTick(() => {

                this.updateFirstPayment();

            });

        },


        /*
         * =====================================================
         * ACTUALIZAR PRIMER PAGO
         * =====================================================
         */
        updateFirstPayment() {

            if (
                this.fields.payment1_cost ===
                undefined
            ) {

                return;

            }


            this.fields.payment1_cost =
                this.totalGeneral.toFixed(2);

        },


        /*
         * =====================================================
         * VALIDAR PRODUCTOS
         * =====================================================
         */
        validateProducts() {

            let valid = true;


            this.errors =
                this.fields.products.map(
                    p => {

                        const rowErrors = {};


                        if (!p.product_id) {

                            rowErrors.product_id =
                                'Debe seleccionar un producto';

                            valid = false;

                        }


                        if (
                            p.quantity <= 0
                        ) {

                            rowErrors.quantity =
                                'Cantidad debe ser mayor a 0';

                            valid = false;

                        }


                        return rowErrors;

                    }
                );


            return valid;

        },


        /*
         * =====================================================
         * COPIAR CAMPOS DE PAGO
         * =====================================================
         */
        copyPaymentFields(
            source,
            target
        ) {

            const regex =
                new RegExp(
                    '^payment' +
                    source +
                    '_'
                );


            this.deletePaymentFields(
                target
            );


            for (
                let field in this.fields
            ) {

                if (
                    regex.test(field)
                ) {

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


        /*
         * =====================================================
         * ELIMINAR CAMPOS DE PAGO
         * =====================================================
         */
        deletePaymentFields(index) {

            const regex =
                new RegExp(
                    '^payment' +
                    index +
                    '_'
                );


            for (
                let field in this.fields
            ) {

                if (
                    regex.test(field)
                ) {

                    delete this.fields[field];

                }

            }

        },


        /*
         * =====================================================
         * ELIMINAR MÉTODO DE PAGO
         * =====================================================
         */
        removePayments(index) {

            for (
                let i = 0;
                i <
                this.fields.payments_count -
                index;
                i++
            ) {

                this.copyPaymentFields(
                    index + i + 1,
                    index + i
                );

            }


            this.fields.payments_count--;


            this.deletePaymentFields(
                this.fields.payments_count + 1
            );

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
   ========================================== */

@media (max-width: 767px) {

    .products-table thead {
        display: none;
    }


    .products-table tbody tr {
        display: block;
        margin-bottom: 16px;
        padding: 12px 14px;
        border: 1px solid #e1e5e9;
        border-radius: 8px;
        background: #fff;
    }


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


    .products-table tbody td[data-label]::before {
        content: attr(data-label);
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
    }


    .products-table tbody td input {
        width: 100%;
        box-sizing: border-box;
    }


    .products-table tbody td[data-label="Precio unitario:"] {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }


    .products-table tbody td[data-label="Precio unitario:"]::before {
        margin-bottom: 0;
    }


    .products-table tbody td[data-label="Subtotal:"] {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }


    .products-table tbody td[data-label="Subtotal:"]::before {
        margin-bottom: 0;
    }


    .products-table tbody td[data-label="Acciones:"] button {
        width: 100%;
    }


    /* Resumen */

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


    .products-table tfoot tr:last-child {
        padding-top: 15px;
        padding-bottom: 15px;
        background: #f8f9fa;
        font-size: 16px;
    }

}

</style>