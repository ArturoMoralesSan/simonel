<template>

    <div>

        <form>

            <div class="form-control">
                <label>
                    Tipo de almacén:
                </label>

                <search-select-field
                    name="warehouse_type"
                    v-model="fields.warehouse_type"
                    :options="warehouseTypes"
                    @input="cargarInventario"
                />

                <field-errors name="warehouse_type">
                </field-errors>
            </div>

        </form>


        <section
            v-if="fields.warehouse_type"
            class="db-panel inventory-panel"
        >

            <h3 class="db-panel__title">
                Inventario de
                {{ fields.warehouse_type }}
            </h3>


            <!-- Cargando -->

            <div
                v-if="loading"
                class="text-center py-4"
            >
                Cargando inventario...
            </div>


            <!-- Inventario -->

            <div
                v-else
                class="inventory-container"
            >

                <!-- Sin inventario -->

                <div
                    v-if="inventory.length === 0"
                    class="inventory-empty"
                >
                    No hay inventario disponible
                    para este tipo de almacén.
                </div>


                <!-- Almacenes -->

                <template
                    v-for="item in inventory"
                >

                    <template
                        v-for="warehouse in item.warehouses"
                    >

                        <!-- Encabezado del almacén -->

                        <div
                            :key="
                                'warehouse-' +
                                item.type +
                                '-' +
                                item.item_id +
                                '-' +
                                warehouse.id
                            "
                            class="warehouse-section"
                        >

                            <div class="warehouse-header">

                                <div>
                                    <strong>
                                        {{ warehouse.name }}
                                    </strong>

                                    <small>
                                        Almacén
                                    </small>
                                </div>

                                <div class="warehouse-total">

                                    <span>
                                        Total
                                    </span>

                                    <strong>
                                        {{ Number(warehouse.quantity).toFixed(2) }}
                                        kg
                                    </strong>

                                </div>

                            </div>


                            <!-- Tabla del almacén -->

                            <div class="table-responsive">

                                <table
                                    class="table size-caption mx-auto md:table--responsive"
                                >

                                    <thead>

                                        <tr class="table-resource__headings">

                                            <th>
                                                Producto / Materia prima
                                            </th>

                                            <th>
                                                Tipo
                                            </th>

                                            <th>
                                                Lote
                                            </th>

                                            <th class="text-center">
                                                Cantidad
                                            </th>

                                            <th class="text-center">
                                                Costo
                                            </th>

                                            <th class="text-center">
                                                Total
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr
                                            v-for="lot in warehouse.lots"
                                            :key="
                                                item.type +
                                                '-' +
                                                item.item_id +
                                                '-' +
                                                warehouse.id +
                                                '-' +
                                                lot.id
                                            "
                                            class="table-resource__row"
                                        >

                                            <!-- Producto -->

                                            <td
                                                data-label="Producto / Materia prima:"
                                            >

                                                <strong>
                                                    {{ item.name }}
                                                </strong>

                                                <br>

                                                <small
                                                    v-if="item.description"
                                                >
                                                    {{ item.description }}
                                                </small>

                                            </td>


                                            <!-- Tipo -->

                                            <td
                                                data-label="Tipo:"
                                            >

                                                <span
                                                    v-if="item.type === 'product'"
                                                >
                                                    Producto terminado
                                                </span>

                                                <span v-else>
                                                    Materia prima
                                                </span>

                                            </td>


                                            <!-- Lote -->

                                            <td
                                                data-label="Lote:"
                                            >

                                                <strong>
                                                    {{ lot.lot_number || '-' }}
                                                </strong>

                                                <br>

                                                <small
                                                    v-if="lot.expiration_date"
                                                    :class="{
                                                        'text-danger':
                                                            lot.is_expired
                                                    }"
                                                >

                                                    <span
                                                        v-if="lot.is_expired"
                                                    >
                                                        ⚠️ Vencido:
                                                    </span>

                                                    <span v-else>
                                                        Caduca:
                                                    </span>

                                                    {{ formatDate(lot.expiration_date) }}

                                                </small>

                                                <small v-else>
                                                    Sin fecha de caducidad
                                                </small>

                                            </td>


                                            <!-- Cantidad -->

                                            <td
                                                class="text-center"
                                                data-label="Cantidad:"
                                            >

                                                <strong>
                                                    {{ Number(lot.quantity).toFixed(2) }}
                                                    kg
                                                </strong>

                                            </td>


                                            <!-- Costo -->

                                            <td
                                                class="text-center"
                                                data-label="Costo:"
                                            >

                                                ${{ Number(lot.cost || 0).toFixed(2) }}

                                            </td>


                                            <!-- Total -->

                                            <td
                                                class="text-center"
                                                data-label="Total:"
                                            >

                                                <strong>
                                                    ${{ Number(lot.total || 0).toFixed(2) }}
                                                </strong>

                                            </td>

                                        </tr>


                                        <!-- Subtotal del artículo dentro del almacén -->

                                        <tr class="warehouse-item-total">

                                            <td
                                                colspan="3"
                                                class="text-right"
                                            >

                                                <strong>
                                                    Total {{ item.name }}
                                                </strong>

                                            </td>

                                            <td
                                                class="text-center"
                                            >

                                                <strong>
                                                    {{ Number(
                                                        warehouse.lots.reduce(
                                                            (sum, lot) =>
                                                                sum +
                                                                Number(
                                                                    lot.quantity || 0
                                                                ),
                                                            0
                                                        )
                                                    ).toFixed(2) }}
                                                    kg
                                                </strong>

                                            </td>

                                            <td></td>

                                            <td
                                                class="text-center"
                                            >

                                                <strong>
                                                    ${{
                                                        Number(
                                                            warehouse.lots.reduce(
                                                                (sum, lot) =>
                                                                    sum +
                                                                    Number(
                                                                        lot.total || 0
                                                                    ),
                                                                0
                                                            )
                                                        ).toFixed(2)
                                                    }}
                                                </strong>

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </template>

                </template>


                <!-- Total general -->

                <div
                    v-if="inventory.length > 0"
                    class="inventory-grand-total"
                >

                    <div class="inventory-grand-total-label">

                        <strong>
                            Total de inventario
                        </strong>

                    </div>

                    <div class="inventory-grand-total-value">

                        <strong>
                            {{ totalInventory }} kg
                        </strong>

                    </div>

                </div>

            </div>

        </section>

    </div>

</template>


<script>

import BaseForm from '../../main/components/forms/base/BaseForm.vue';

export default {

    extends: BaseForm,

    props: {

        warehouseTypes: {

            required: true,

            type: [Array, Object]

        }

    },


    data() {

        return {

            inventory: [],

            loading: false,

            fields: {

                warehouse_type: null

            }

        };

    },


    computed: {

        totalInventory() {

            const total = this.inventory.reduce(

                (sum, item) => {

                    return sum + Number(item.quantity || 0);

                },

                0

            );

            return total.toFixed(2);

        }

    },


    methods: {

        async cargarInventario(warehouseType) {

            if (!warehouseType) {

                this.inventory = [];

                return;

            }


            this.loading = true;


            try {

                const response = await window.axios.get(

                    '/api/inventory-warehouse',

                    {

                        params: {

                            warehouse_type: warehouseType

                        }

                    }

                );


                this.inventory = Array.isArray(

                    response.data.inventory

                )

                    ? response.data.inventory

                    : [];


            } catch (error) {

                console.error(

                    'Error cargando inventario:',

                    error

                );

                this.inventory = [];

            } finally {

                this.loading = false;

            }

        },


        formatDate(date) {

            if (!date) {

                return '-';

            }


            const parts = String(date)

                .substring(0, 10)

                .split('-');


            if (parts.length !== 3) {

                return date;

            }


            return (

                parts[2] +

                '/' +

                parts[1] +

                '/' +

                parts[0]

            );

        }

    }

};

</script>


<style scoped>

.text-danger {

    color: #dc3545;

    font-weight: bold;

}


/*
|--------------------------------------------------------------------------
| Contenedor de inventario
|--------------------------------------------------------------------------
*/

.inventory-container {

    width: 100%;

}


/*
|--------------------------------------------------------------------------
| Sección de almacén
|--------------------------------------------------------------------------
*/

.warehouse-section {

    margin-bottom: 30px;

    border: 1px solid rgba(0, 0, 0, .08);

    border-radius: 8px;

    overflow: hidden;

}


/*
|--------------------------------------------------------------------------
| Encabezado del almacén
|--------------------------------------------------------------------------
*/

.warehouse-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 15px 18px;

    background: rgba(0, 0, 0, .035);

    border-bottom: 1px solid rgba(0, 0, 0, .08);

}


.warehouse-header strong {

    display: block;

    font-size: 17px;

}


.warehouse-header small {

    display: block;

    margin-top: 3px;

    opacity: .65;

}


.warehouse-total {

    text-align: right;

}


.warehouse-total span {

    display: block;

    font-size: 12px;

    opacity: .65;

}


.warehouse-total strong {

    font-size: 18px;

}


/*
|--------------------------------------------------------------------------
| Tabla
|--------------------------------------------------------------------------
*/

.warehouse-section .table {

    margin-bottom: 0;

}


/*
|--------------------------------------------------------------------------
| Total por artículo
|--------------------------------------------------------------------------
*/

.warehouse-item-total {

    background: rgba(0, 0, 0, .025);

}


.warehouse-item-total td {

    border-top: 1px solid rgba(0, 0, 0, .08);

}


/*
|--------------------------------------------------------------------------
| Total general
|--------------------------------------------------------------------------
*/

.inventory-grand-total {

    display: flex;

    justify-content: flex-end;

    align-items: center;

    gap: 30px;

    padding: 18px 20px;

    margin-top: 10px;

    border-top: 2px solid rgba(0, 0, 0, .12);

}


.inventory-grand-total-label {

    font-size: 17px;

}


.inventory-grand-total-value {

    font-size: 20px;

}


/*
|--------------------------------------------------------------------------
| Sin inventario
|--------------------------------------------------------------------------
*/

.inventory-empty {

    text-align: center;

    padding: 30px;

    opacity: .7;

}


/*
|--------------------------------------------------------------------------
| Responsive
|--------------------------------------------------------------------------
*/

@media (max-width: 767px) {

    .warehouse-header {

        align-items: flex-start;

        gap: 15px;

    }


    .warehouse-total {

        min-width: 100px;

    }


    .inventory-grand-total {

        display: block;

        text-align: right;

    }


    .inventory-grand-total-label,

    .inventory-grand-total-value {

        display: block;

        width: 100%;

        padding: 5px 0;

    }

}

</style>