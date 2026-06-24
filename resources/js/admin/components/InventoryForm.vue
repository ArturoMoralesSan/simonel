<template>
    <div>
        <form>
            <div class="form-control">
                <label>Cliente:</label>
                <search-select-field
                    name="client_id"
                    v-model="fields.client_id"
                    :options="clients"
                    @input="cargarCliente"
                />
                <field-errors name="client_id"></field-errors>
            </div>

            <div v-if="fields.client_id">
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Inventario de {{ clienteNombre }}
                    </h3>
                    <table class="table size-caption mx-auto md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="productItem in inventory" class="table-resource__row" :key="productItem.id">
                                <td>
                                    {{ productItem.product.manufactured.name }}
                                    {{ productItem.product.manufactured.description }}
                                </td>
                                <td>{{ productItem.quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <div v-if="esMayorista" class="tabs mb-4">
                    <a
                        :class="['btn tab-btn', { active: fields.type === 'entrada' }]"
                        @click="fields.type = 'entrada'"
                    >
                        En Camara de refrigeración
                    </a>

                    <a
                        :class="['btn tab-btn', { active: fields.type === 'salida' }]"
                        @click="fields.type = 'salida'"
                    >
                        En piso
                    </a>

                    <a
                        :class="['btn tab-btn', { active: fields.type === 'resumen' }]"
                        @click="fields.type = 'resumen'"
                    >
                        Resumen
                    </a>
                </div>

                <section v-if="!esMayorista || fields.type === 'entrada' || fields.type === 'salida'" class="db-panel">
                    <h3 class="db-panel__title">
                        {{ esMayorista ? 'Agregar ' + fields.type : 'Agregar inventario' }}
                    </h3>

                    <div v-for="index in fields.product_count" :key="'entrada-'+index" class="mb-4 product-row">

                        <button
                            type="button"
                            class="btn btn--danger btn--sm mt-4 btn-delete"
                            v-if="index >= 2"
                            @click="quitarProducto(index)"
                        >
                            X
                        </button>

                        <div class="md:row">
                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label>Fecha de inicio</label>
                                    <date-field
                                        :name="'inventory' + index + '_date'"
                                        v-model="fields['inventory' + index + '_date']"
                                    />
                                    <field-errors :name="'inventory' + index + '_date'"></field-errors>
                                </div>
                            </div>

                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label>Producto</label>
                                    <select-field
                                        :name="'inventory' + index + '_product_id'"
                                        v-model="fields['inventory' + index + '_product_id']"
                                        :options="productsOptions"
                                    />
                                    <field-errors :name="'inventory' + index + '_product_id'"></field-errors>
                                </div>
                            </div>

                            <div class="md:col-1/3">
                                <div class="form-control">
                                    <label>Cantidad</label>
                                    <text-field
                                        :name="'inventory' + index + '_quantity'"
                                        v-model="fields['inventory' + index + '_quantity']"
                                        type="number"
                                        step="any"
                                    />
                                    <field-errors :name="'inventory' + index + '_quantity'"></field-errors>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button
                        class="btn btn--sm btn--primary mb-2"
                        type="button"
                        @click="fields.product_count++"
                    >
                        {{
                            esMayorista
                                ? `Agregar productos a ${fields.type == 'entrada' ? 'camara de refrigeración' : 'piso'}`
                                : 'Agregar producto'
                        }}
                    </button>
                </section>

                <div v-if="!esMayorista || fields.type === 'entrada' || fields.type === 'salida'" class="text-center p-8">
                    <form-button class="btn--primary btn--wide">
                        Guardar
                    </form-button>
                </div>

                <!-- HISTORIAL ENTRADAS -->
                <div v-if="esMayorista && fields.type==='entrada'">
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Historial de camara de refrigeración
                        </h3>

                        <table class="table size-caption mx-auto md:table--responsive">
                            <thead>
                                <tr class="table-resource__headings">
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="productItem in movementsEntradas" :key="productItem.id">
                                    <td>{{ formatearFecha(productItem.date) }}</td>
                                    <td>
                                        {{ productItem.inventory.product.manufactured.name }}
                                        {{ productItem.inventory.product.manufactured.description }}
                                    </td>
                                    <td>{{ productItem.quantity }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                </div>

                <!-- HISTORIAL SALIDAS -->
                <div v-if="esMayorista && fields.type==='salida'">
                    <section class="db-panel">
                        <h3 class="db-panel__title">
                            Historial de producto en piso
                        </h3>

                        <table class="table size-caption mx-auto md:table--responsive">
                            <thead>
                                <tr class="table-resource__headings">
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="productItem in movementsSalidas" :key="productItem.id">
                                    <td>{{ formatearFecha(productItem.date) }}</td>
                                    <td>
                                        {{ productItem.inventory.product.manufactured.name }}
                                        {{ productItem.inventory.product.manufactured.description }}
                                    </td>
                                    <td>{{ productItem.quantity }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                </div>

                <!-- RESUMEN -->
                <div v-if="esMayorista && fields.type==='resumen'">
                    <section class="db-panel">
                        <h3 class="db-panel__title">Resumen de Inventario</h3>

                        <table class="table size-caption mx-auto md:table--responsive">
                            <thead>
                                <tr class="table-resource__headings">
                                    <th>Producto</th>
                                    <th>Stock actual</th>
                                    <th>Total en camara de refrigeración</th>
                                    <th>Total en piso</th>
                                    <th>Promedio</th>
                                    <th>Recomendación de compra</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(producto, nombre) in resumen" :key="nombre">
                                    <td>{{ nombre }}</td>
                                    <td>{{ producto.stock }}</td>
                                    <td>{{ producto.entradas }}</td>
                                    <td>{{ producto.salidas }}</td>
                                    <td>{{ producto.promedio }}</td>
                                    <td>{{ producto.sugerencia }}</td>
                                </tr>
                            </tbody>
                        </table>
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
        clients: { required: true, type: [Array, Object] },
        products: { required: true, type: [Array, Object] },
    },

    data() {
        return {
            clienteNombre: '',
            clienteTipo: 'mayorista',
            inventory: {},
            inventoryOptions: {},
            movementsEntradas: {},
            movementsSalidas: {},
            pendingProducts: {},
            pendingProductsData: {},
            fields: {
                product_count: 1,
                type: 'resumen',
            },
        };
    },
    watch: {
        fields: {
            deep: true,
            handler() {

                if (this.fields.type === 'salida') return;

                Object.keys(this.fields).forEach(key => {

                    if (!key.includes('_product_id')) return;

                    const index = key.replace('inventory', '').replace('_product_id', '');
                    const productId = this.fields[key];
                    const product = this.pendingProductsData?.[productId];

                    if (!product) return;

                    if (this.fields.type === 'entrada' && product.quantity !== undefined) {
                        const qtyKey = `inventory${index}_quantity`;

                        if (!this.fields[qtyKey]) {
                            this.$set(this.fields, qtyKey, product.quantity);
                        }
                    }

                    if (product.sale_id) {
                        this.$set(this.fields, `inventory${index}_sale_id`,product.sale_id);
                    }
                });
            }
        },
        'fields.type'(newType, oldType) {

            if (newType === oldType) return;

            this.clearInventorySelection();
        }
    },
    computed: {
        esMayorista() {
            return this.clienteTipo === 'mayorista';
        },

        productsOptions() {
            if (!this.esMayorista) return this.pendingProducts;
            if (this.fields.type === 'entrada') return this.pendingProducts;
            if (this.fields.type === 'salida') return this.inventoryOptions;
            return {};
        },

        resumen() {
            const resumen = {};
            const productos = Object.values(this.inventory).map(i => i.product.manufactured.name);

            productos.forEach(p => {
                const inv = Object.values(this.inventory).find(i => i.product.manufactured.name === p);
                const stock = inv ? Number(inv.quantity || 0) : 0;

                const entradas = Object.values(this.movementsEntradas || {}).reduce((acc, m) =>
                    m.inventory.product.manufactured.name === p ? acc + Number(m.quantity || 0) : acc
                , 0);

                const salidasArray = Object.values(this.movementsSalidas || {}).filter(
                    m => m.inventory.product.manufactured.name === p
                );

                const salidas = salidasArray.reduce((acc, m) => acc + Number(m.quantity || 0), 0);

                let promedio = 0;
                let sugerencia = 0;

                if (salidasArray.length > 0) {
                    const fechas = salidasArray.map(m => new Date(m.date));
                    const minFecha = new Date(Math.min(...fechas));
                    const maxFecha = new Date(Math.max(...fechas));
                    const diffMs = maxFecha - minFecha;
                    const dias = diffMs > 0 ? Math.ceil(diffMs / (1000 * 60 * 60 * 24)) : 1;

                    promedio = (salidas / dias).toFixed(1);
                    sugerencia = Math.max(0, Math.ceil(promedio * dias - stock));
                }

                resumen[p] = { stock, entradas, salidas, promedio, sugerencia };
            });

            return resumen;
        }
    },

    methods: {
        clearInventorySelection() {
            Object.keys(this.fields).forEach(key => {

                if (key.includes('_product_id')) {
                    this.$delete(this.fields, key);
                }

                if (key.includes('_quantity')) {
                    this.$delete(this.fields, key);
                }

                if (key.includes('_sale_id')) {
                    this.$delete(this.fields, key);
                }
            });
        },
        formatearFecha(fecha) {
            if (!fecha) return '';
            const f = new Date(fecha);
            const fechaLocal = new Date(f.getTime() + f.getTimezoneOffset() * 60000);
            const dia = fechaLocal.getDate().toString().padStart(2, '0');
            const mes = (fechaLocal.getMonth() + 1).toString().padStart(2, '0');
            const año = fechaLocal.getFullYear();
            return `${dia}/${mes}/${año}`;
        },

        async cargarCliente(clientId) {
            const clientsArray = Object.entries(this.clients).map(([id, nombre]) => ({
                id: Number(id),
                full_name: nombre
            }));

            const client = clientsArray.find(c => c.id === Number(clientId));
            if (!client) return;

            this.clienteNombre = client.full_name;

            const response = await window.axios.get(`/api/inventory-clients?client=${clientId}`);

            this.clienteTipo = response.data.customer_type || 'mayorista';

            if (this.clienteTipo === 'minorista') {
                this.fields.type = 'entrada';
            } else {
                this.fields.type = 'resumen';
            }

            this.inventory = Array.isArray(response.data.inventory) ? [...response.data.inventory] : [];
            this.inventoryOptions = {};

            this.inventory.forEach(item => {
                if (Number(item.quantity) <= 0) return;
                
                this.inventoryOptions[item.product_id] =
                    item.product.manufactured.name +
                    ' (' + item.quantity + ' disponibles)';
            });

            this.movementsEntradas = response.data.movementsEntradas || [];
            this.movementsSalidas = response.data.movementsSalidas || [];
            this.pendingProducts = response.data.pendingProducts || {};
            this.pendingProductsData = response.data.pendingProductsData || {};

        },

        quitarProducto(index) {
            this.$delete(this.fields, 'inventory' + index + '_date');
            this.$delete(this.fields, 'inventory' + index + '_product_id');
            this.$delete(this.fields, 'inventory' + index + '_quantity');

            if (this.fields.product_count > 1) {
                this.fields.product_count--;
            }
        },
    },
};
</script>
<style scoped>
/* Tabs con Bootstrap */
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
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-content {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        width: 400px;
        max-width: 90%;
    }

</style>
