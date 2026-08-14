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
            class="db-panel"
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


            <!-- Tabla -->

            <table
                v-else
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
                            Almacén
                        </th>

                        <th>
                            Lote
                        </th>

                        <th class="text-center">
                            Cantidad
                        </th>

                        <th class="text-center">
                            Total
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <!-- Productos -->

                    <template
                        v-for="item in inventory"
                    >

                        <template
                            v-for="warehouse in item.warehouses"
                        >

                            <tr
                                v-for="(lot, lotIndex) in warehouse.lots"
                                :key="item.type + '-' + item.item_id + '-' + warehouse.id + '-' + lot.id"
                                class="table-resource__row"
                            >

                                <!-- Producto -->

                                <td>

                                    <strong>
                                        {{ item.name }}
                                    </strong>

                                    <br>

                                    <small v-if="item.description">
                                        {{ item.description }}
                                    </small>

                                </td>


                                <!-- Tipo -->

                                <td>

                                    <span v-if="item.type === 'product'">
                                        Producto terminado
                                    </span>

                                    <span v-else>
                                        Materia prima
                                    </span>

                                </td>


                                <!-- Almacén -->

                                <td>

                                    {{ warehouse.name }}

                                </td>


                                <!-- Lote -->

                                <td>

                                    <strong>
                                        {{ lot.lot_number || '-' }}
                                    </strong>

                                    <br>

                                    <small
                                        v-if="lot.expiration_date"
                                        :class="{
                                            'text-danger': lot.is_expired
                                        }"
                                    >

                                        <span v-if="lot.is_expired">
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


                                <!-- Cantidad del lote -->

                                <td class="text-center">

                                    {{ Number(lot.quantity).toFixed(2) }}

                                </td>


                                <!-- Total del producto -->

                                <td
                                    v-if="lotIndex === 0"
                                    :rowspan="warehouse.lots.length"
                                    class="text-center"
                                >

                                    <strong>
                                        {{ Number(item.quantity).toFixed(2) }}
                                    </strong>

                                </td>

                            </tr>

                        </template>

                    </template>


                    <!-- Sin inventario -->

                    <tr
                        v-if="inventory.length === 0"
                    >

                        <td
                            colspan="6"
                            class="text-center"
                        >

                            No hay inventario disponible
                            para este tipo de almacén.

                        </td>

                    </tr>

                </tbody>


                <!-- Total general -->

                <tfoot
                    v-if="inventory.length > 0"
                >

                    <tr>

                        <th
                            colspan="5"
                            class="text-right"
                        >
                            Total de inventario
                        </th>

                        <th class="text-center">

                            {{ totalInventory }}

                        </th>

                    </tr>

                </tfoot>

            </table>

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

</style>