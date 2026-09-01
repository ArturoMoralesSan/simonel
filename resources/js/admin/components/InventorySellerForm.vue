<template>
    <div>
        <form>

            <!-- VENDEDOR -->
            <div class="form-control">
                <label>Vendedor:</label>

                <select-field
                    name="seller_id"
                    v-model="fields.seller_id"
                    :options="sellers"
                    @input="cargarVendedor"
                />

                <field-errors name="seller_id" />
            </div>

            <div v-if="fields.seller_id">

                <!-- =====================================================
                     INVENTARIO ACTUAL
                ====================================================== -->
                <section class="db-panel">

                    <div class="db-panel__header">

                        <div class="db-panel__header-info">

                            <h3 class="db-panel__title">
                                Inventario de vendedores
                            </h3>

                            <p
                                v-if="assignment"
                                class="text-success"
                            >
                                Asignación abierta del día:
                                {{ formatearFecha(assignment.assignment_date) }}
                            </p>

                            <p
                                v-else
                                class="text-muted"
                            >
                                No existe una asignación abierta para hoy.
                            </p>

                        </div>

                        <!-- CERRAR DÍA -->
                        <div
                            v-if="assignment"
                            class="close-day-container"
                        >
                            <button
                                type="button"
                                class="btn btn--danger"
                                @click="cerrarDia"
                                :disabled="cerrandoDia"
                            >
                                {{
                                    cerrandoDia
                                        ? 'Cerrando...'
                                        : 'Cerrar día'
                                }}
                            </button>
                        </div>

                    </div>


                    <!-- INVENTARIO -->
                    <table
                        class="table size-caption mx-auto md:table--responsive"
                    >

                        <thead>
                            <tr class="table-resource__headings">
                                <th>Producto</th>
                                <th>Asignado</th>
                                <th>Disponible</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr
                                v-for="productItem in inventory"
                                :key="
                                    productItem.product_lot_id ||
                                    productItem.id
                                "
                                class="table-resource__row"
                            >

                                <td data-label="Producto">

                                    {{
                                        productItem.product_lot?.product?.manufactured?.name
                                        || productItem.product_lot?.product?.name
                                        || productItem.product?.manufactured?.name
                                        || productItem.product?.name
                                        || 'Producto'
                                    }}

                                    <span
                                        v-if="
                                            productItem.product_lot?.lot_number
                                        "
                                    >
                                        -
                                        Lote #{{
                                            productItem.product_lot.lot_number
                                        }}
                                    </span>

                                </td>

                                <td data-label="Asignado">

                                    {{
                                        Number(
                                            productItem.assigned_quantity
                                            || productItem.quantity
                                            || 0
                                        ).toFixed(2)
                                    }}

                                </td>

                                <td data-label="Disponible">

                                    <strong>
                                        {{
                                            Number(
                                                productItem.available_quantity
                                                ?? productItem.quantity
                                                ?? 0
                                            ).toFixed(2)
                                        }}
                                    </strong>

                                </td>

                            </tr>


                            <tr v-if="!inventory.length">

                                <td
                                    colspan="3"
                                    class="text-center"
                                >
                                    No hay inventario asignado.
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </section>


                <!-- =====================================================
                     TABS
                ====================================================== -->
                <div class="tabs mb-4">

                    <a
                        :class="[
                            'btn tab-btn',
                            {
                                active:
                                    fields.type === 'asignado'
                            }
                        ]"
                        @click="fields.type = 'asignado'"
                    >
                        Asignar
                    </a>

                    <a
                        :class="[
                            'btn tab-btn',
                            {
                                active:
                                    fields.type === 'resumen'
                            }
                        ]"
                        @click="fields.type = 'resumen'"
                    >
                        Resumen
                    </a>

                    <a
                        :class="[
                            'btn tab-btn',
                            {
                                active:
                                    fields.type === 'historial'
                            }
                        ]"
                        @click="fields.type = 'historial'"
                    >
                        Historial
                    </a>

                </div>


                <!-- =====================================================
                     ASIGNAR INVENTARIO
                ====================================================== -->
                <section
                    v-if="fields.type === 'asignado'"
                    class="db-panel"
                >

                    <h3 class="db-panel__title">
                        Asignar inventario
                    </h3>


                    <div
                        v-for="index in fields.product_count"
                        :key="'assignment-' + index"
                        class="mb-4 product-row"
                    >

                        <!-- ELIMINAR -->
                        <button
                            type="button"
                            class="btn btn--danger btn--sm mt-4 btn-delete"
                            v-if="index >= 2"
                            @click="quitarProducto(index)"
                        >
                            X
                        </button>


                        <div class="md:row">

                            <!-- FECHA -->
                            <div class="md:col-1/3">

                                <div class="form-control">

                                    <label>
                                        Fecha
                                    </label>

                                    <date-field
                                        :name="
                                            'assignment' +
                                            index +
                                            '_date'
                                        "
                                        v-model="
                                            fields[
                                                'assignment' +
                                                index +
                                                '_date'
                                            ]
                                        "
                                    />

                                    <field-errors
                                        :name="
                                            'assignment' +
                                            index +
                                            '_date'
                                        "
                                    />

                                </div>

                            </div>


                            <!-- PRODUCTO / LOTE -->
                            <div class="md:col-1/3">

                                <div class="form-control">

                                    <label>
                                        Producto / Lote
                                    </label>

                                    <select-field
                                        :name="
                                            'assignment' +
                                            index +
                                            '_product_lot_id'
                                        "
                                        v-model="
                                            fields[
                                                'assignment' +
                                                index +
                                                '_product_lot_id'
                                            ]
                                        "
                                        :options="productLotOptions"
                                    />

                                    <field-errors
                                        :name="
                                            'assignment' +
                                            index +
                                            '_product_lot_id'
                                        "
                                    />

                                </div>

                            </div>


                            <!-- CANTIDAD -->
                            <div class="md:col-1/3">

                                <div class="form-control">

                                    <label>
                                        Cantidad
                                    </label>

                                    <text-field
                                        :name="
                                            'assignment' +
                                            index +
                                            '_quantity'
                                        "
                                        v-model="
                                            fields[
                                                'assignment' +
                                                index +
                                                '_quantity'
                                            ]
                                        "
                                        type="number"
                                        min="0"
                                        step="any"
                                    />

                                    <field-errors
                                        :name="
                                            'assignment' +
                                            index +
                                            '_quantity'
                                        "
                                    />

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- AGREGAR PRODUCTO -->
                    <button
                        class="btn btn--sm btn--primary mb-2"
                        type="button"
                        @click="fields.product_count++"
                    >
                        Agregar producto
                    </button>

                </section>


                <!-- =====================================================
                     GUARDAR
                ====================================================== -->
                <div
                    v-if="fields.type === 'asignado'"
                    class="text-center p-8"
                >

                    <form-button
                        class="btn--primary btn--wide"
                    >
                        Guardar asignación
                    </form-button>

                </div>


                <!-- =====================================================
                     RESUMEN
                ====================================================== -->
                <div
                    v-if="fields.type === 'resumen'"
                >

                    <section class="db-panel">

                        <h3 class="db-panel__title">
                            Resumen de inventario del vendedor
                        </h3>


                        <table
                            class="table size-caption mx-auto md:table--responsive"
                        >

                            <thead>

                                <tr class="table-resource__headings">

                                    <th>
                                        Producto
                                    </th>

                                    <th>
                                        Asignado
                                    </th>

                                    <th>
                                        Disponible
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr
                                    v-for="(
                                        producto,
                                        nombre
                                    ) in resumen"
                                    :key="nombre"
                                >

                                    <td data-label="Producto">
                                        {{ nombre }}
                                    </td>

                                    <td data-label="Asignado">

                                        {{
                                            Number(
                                                producto.asignado
                                            ).toFixed(2)
                                        }}

                                    </td>

                                    <td data-label="Disponible">

                                        <strong>
                                            {{
                                                Number(
                                                    producto.disponible
                                                ).toFixed(2)
                                            }}
                                        </strong>

                                    </td>

                                </tr>


                                <tr
                                    v-if="
                                        !Object.keys(resumen).length
                                    "
                                >

                                    <td
                                        colspan="3"
                                        class="text-center"
                                    >
                                        No hay información para mostrar.
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </section>

                </div>


                <!-- =====================================================
                     HISTORIAL
                ====================================================== -->
                <div
                    v-if="fields.type === 'historial'"
                >

                    <section class="db-panel">

                        <h3 class="db-panel__title">
                            Historial del vendedor
                        </h3>


                        <div
                            v-if="!history.length"
                            class="text-center py-4 text-muted"
                        >
                            No hay historial de movimientos.
                        </div>


                        <!-- ASIGNACIONES -->
                        <div
                            v-for="item in history"
                            :key="item.id"
                            class="history-card"
                        >

                            <!-- CABECERA -->
                            <div class="history-header">

                                <div>

                                    <strong>
                                        Asignación #{{ item.id }}
                                    </strong>

                                    <span>
                                        {{
                                            formatearFecha(
                                                item.assignment_date
                                            )
                                        }}
                                    </span>

                                    <span
                                        v-if="item.closed_at"
                                        class="text-muted"
                                    >
                                        Cerrada:
                                        {{
                                            formatearFechaHora(
                                                item.closed_at
                                            )
                                        }}
                                    </span>

                                </div>


                                <span
                                    :class="[
                                        'status-badge',
                                        item.status === 'closed'
                                            ? 'status-closed'
                                            : 'status-open'
                                    ]"
                                >

                                    {{
                                        item.status === 'closed'
                                            ? 'Cerrada'
                                            : 'Abierta'
                                    }}

                                </span>

                            </div>


                            <!-- MOVIMIENTOS -->
                            <div
                                v-if="
                                    item.movements &&
                                    item.movements.length
                                "
                            >

                                <table
                                    class="table size-caption mx-auto md:table--responsive"
                                >

                                    <thead>

                                        <tr
                                            class="table-resource__headings"
                                        >

                                            <th>
                                                Fecha
                                            </th>

                                            <th>
                                                Producto
                                            </th>

                                            <th>
                                                Movimiento
                                            </th>

                                            <th>
                                                Cantidad
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr
                                            v-for="movement in item.movements"
                                            :key="movement.id"
                                            class="table-resource__row"
                                        >

                                            <td data-label="Fecha">

                                                {{
                                                    formatearFechaHora(
                                                        movement.created_at
                                                    )
                                                }}

                                            </td>


                                            <td data-label="Producto">

                                                {{
                                                    movement.product_lot?.product?.manufactured?.name
                                                    || movement.product_lot?.product?.name
                                                    || 'Producto'
                                                }}

                                                <span
                                                    v-if="
                                                        movement.product_lot?.lot_number
                                                    "
                                                >
                                                    -
                                                    Lote #{{
                                                        movement.product_lot.lot_number
                                                    }}
                                                </span>

                                            </td>


                                            <td data-label="Movimiento">

                                                <span
                                                    :class="
                                                        movementClass(
                                                            movement.type
                                                        )
                                                    "
                                                >
                                                    {{
                                                        movementLabel(
                                                            movement.type
                                                        )
                                                    }}
                                                </span>

                                            </td>


                                            <td data-label="Cantidad">

                                                {{
                                                    Number(
                                                        movement.quantity
                                                    ).toFixed(2)
                                                }}

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>


                            <div
                                v-else
                                class="text-muted py-3"
                            >
                                No hay movimientos registrados.
                            </div>

                        </div>

                    </section>

                </div>

            </div>

        </form>
    </div>
</template>


<script>

import BaseForm from '../../main/components/forms/base/BaseForm.vue';

export default {

    extends: BaseForm,

    props: {

        sellers: {
            required: true,
            type: [Array, Object]
        },

        productLotOptions: {
            required: true,
            type: [Array, Object]
        },

    },


    data() {

        return {

            vendedorNombre: '',

            inventory: [],

            assignment: null,

            movements: [],

            history: [],

            cerrandoDia: false,

            fields: {

                seller_id: null,

                product_count: 1,

                type: 'resumen',

            },

        };

    },


    computed: {

        productsOptions() {

            return this.productLotOptions || {};

        },


        resumen() {

            const resumen = {};

            Object.values(
                this.inventory || []
            ).forEach(item => {

                const product =
                    item.product_lot?.product?.manufactured?.name
                    || item.product_lot?.product?.name
                    || item.product?.manufactured?.name
                    || item.product?.name
                    || 'Producto';


                if (!resumen[product]) {

                    resumen[product] = {

                        asignado: 0,

                        disponible: 0

                    };

                }


                resumen[product].asignado += Number(

                    item.assigned_quantity
                    || item.quantity
                    || 0

                );


                resumen[product].disponible += Number(

                    item.available_quantity
                    ?? item.quantity
                    ?? 0

                );

            });


            return resumen;

        }

    },


    methods: {

        async cargarVendedor(sellerId) {

            if (!sellerId) {

                this.vendedorNombre = '';

                this.inventory = [];

                this.assignment = null;

                this.movements = [];

                this.history = [];

                return;

            }


            const sellersArray =
                Object.entries(
                    this.sellers
                ).map(
                    ([id, nombre]) => ({
                        id: Number(id),
                        full_name: nombre
                    })
                );


            const seller = sellersArray.find(
                s =>
                    s.id === Number(sellerId)
            );


            if (!seller) {
                return;
            }


            this.vendedorNombre =
                seller.full_name;


            try {

                const response =
                    await window.axios.get(
                        `/api/inventory-sellers?seller=${sellerId}`
                    );


                /*
                 * ASIGNACIÓN ABIERTA DE HOY
                 */

                this.assignment =
                    response.data.assignment
                    || null;


                /*
                 * INVENTARIO ACTUAL
                 */

                this.inventory =
                    Array.isArray(
                        response.data.inventory
                    )
                        ? [
                            ...response.data.inventory
                        ]
                        : [];


                /*
                 * TODOS LOS MOVIMIENTOS
                 *
                 * Aquí vienen:
                 *
                 * assignment
                 * sale
                 * return
                 *
                 * incluyendo asignaciones cerradas.
                 */

                this.movements =
                    Array.isArray(
                        response.data.movements
                    )
                        ? [
                            ...response.data.movements
                        ]
                        : [];


                /*
                 * CONSTRUIR HISTORIAL
                 */

                this.history =
                    this.construirHistorial(
                        this.movements
                    );

            } catch (error) {

                console.error(
                    'Error cargando inventario del vendedor:',
                    error
                );

                this.inventory = [];

                this.assignment = null;

                this.movements = [];

                this.history = [];

            }

        },


        construirHistorial(movements) {

            if (!Array.isArray(movements)) {
                return [];
            }


            const agrupado = {};


            movements.forEach(movement => {

                const assignmentId =
                    movement.assignment_id
                    || movement.assignment?.id;


                if (!assignmentId) {
                    return;
                }


                if (!agrupado[assignmentId]) {

                    const assignment =
                        movement.assignment || {};


                    agrupado[assignmentId] = {

                        id: assignmentId,

                        seller_id:
                            movement.seller_id,

                        assignment_date:
                            assignment.assignment_date
                            || null,

                        status:
                            assignment.status
                            || 'closed',

                        closed_at:
                            assignment.closed_at
                            || null,

                        notes:
                            assignment.notes
                            || null,

                        movements: []

                    };

                }


                agrupado[
                    assignmentId
                ].movements.push(
                    movement
                );

            });


            return Object.values(agrupado)
                .sort((a, b) => {

                    const dateA =
                        new Date(
                            a.assignment_date || 0
                        ).getTime();

                    const dateB =
                        new Date(
                            b.assignment_date || 0
                        ).getTime();

                    return dateB - dateA;

                });

        },


        async cerrarDia() {

            if (!this.assignment?.id) {
                return;
            }


            const result =
                await window.swal({

                    title: '¿Cerrar día?',

                    text:
                        'Se devolverá al lote de producto terminado todo el producto que el vendedor tenga disponible.',

                    icon: 'warning',

                    buttons: [
                        'Cancelar',
                        'Sí, cerrar día'
                    ],

                    dangerMode: true,

                });


            if (!result) {
                return;
            }


            this.cerrandoDia = true;


            try {

                await window.axios.post(
                    `/admin/inventario-vendedores/${this.assignment.id}/cerrar`
                );


                await window.swal(

                    'Día cerrado',

                    'La asignación del vendedor ha sido cerrada correctamente.',

                    'success'

                );


                /*
                 * Recargar información.
                 *
                 * La asignación ya no aparecerá como open.
                 *
                 * Pero sus movimientos continuarán
                 * apareciendo en movements.
                 */

                await this.cargarVendedor(
                    this.fields.seller_id
                );


                /*
                 * Mostrar historial automáticamente.
                 */

                this.fields.type = 'historial';


            } catch (error) {

                console.error(
                    'Error cerrando el día:',
                    error
                );


                let message =
                    'No fue posible cerrar el día.';


                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {

                    message =
                        error.response.data.message;

                }


                await window.swal(

                    'Error',

                    message,

                    'error'

                );

            } finally {

                this.cerrandoDia = false;

            }

        },


        formatearFecha(fecha) {

            if (!fecha) {
                return '';
            }


            /*
             * YYYY-MM-DD
             */

            if (
                typeof fecha === 'string' &&
                /^\d{4}-\d{2}-\d{2}$/.test(fecha)
            ) {

                const [
                    year,
                    month,
                    day
                ] = fecha.split('-');


                return `${day}/${month}/${year}`;

            }


            const date =
                new Date(fecha);


            if (isNaN(date.getTime())) {
                return fecha;
            }


            return date.toLocaleDateString(
                'es-MX',
                {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                }
            );

        },


        formatearFechaHora(fecha) {

            if (!fecha) {
                return '';
            }


            const date =
                new Date(fecha);


            if (isNaN(date.getTime())) {
                return fecha;
            }


            return date.toLocaleString(
                'es-MX',
                {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }
            );

        },


        movementLabel(type) {

            switch (type) {

                case 'assignment':
                    return 'Asignación';

                case 'sale':
                    return 'Venta';

                case 'return':
                    return 'Devolución';

                default:
                    return type;

            }

        },


        movementClass(type) {

            switch (type) {

                case 'assignment':
                    return 'movement-assignment';

                case 'sale':
                    return 'movement-sale';

                case 'return':
                    return 'movement-return';

                default:
                    return '';

            }

        },


        quitarProducto(index) {

            this.$delete(
                this.fields,
                'assignment' +
                index +
                '_date'
            );


            this.$delete(
                this.fields,
                'assignment' +
                index +
                '_product_lot_id'
            );


            this.$delete(
                this.fields,
                'assignment' +
                index +
                '_quantity'
            );


            if (
                this.fields.product_count > 1
            ) {

                this.fields.product_count--;

            }

        }

    }

};

</script>


<style scoped>

.tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}


.tab-btn {
    padding: 0.5rem 1rem;
    border: 1px solid #ccc;
    background: #f9f9f9;
    cursor: pointer;
    border-radius: 4px 4px 0 0;
    font-weight: 600;
}


.tab-btn.active {
    background: #436eb3;
    color: white;
    border-bottom: 1px solid white;
}


/*
 * Encabezado de Inventario de vendedores
 */

.db-panel__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 1rem;
}


.db-panel__header-info {
    flex: 1;
}


.db-panel__header-info .db-panel__title {
    margin-bottom: 0.35rem;
}


.db-panel__header-info p {
    margin: 0;
}


.close-day-container {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}


.text-success {
    color: #198754;
    font-weight: 600;
}


.text-muted {
    color: #6c757d;
}


.product-row {
    position: relative;
    padding-right: 2.5rem;
}


.btn-delete {
    position: absolute;
    top: 1rem;
    right: .05rem;
    background-color: #e53e3e;
    color: white;
    border: none;
    width: 2rem;
    height: 2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease;
}


.btn-delete:hover {
    background-color: #c53030;
}


/*
 * HISTORIAL
 */

.history-card {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
}


.history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    gap: 15px;
}


.history-header > div {
    display: flex;
    flex-direction: column;
    gap: 4px;
}


.status-badge {
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}


.status-open {
    background: #d1e7dd;
    color: #198754;
}


.status-closed {
    background: #e9ecef;
    color: #495057;
}


.movement-assignment {
    font-weight: 600;
}


.movement-sale {
    font-weight: 600;
}


.movement-return {
    font-weight: 600;
}

</style>