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
                                <td>{{ productItem.product.name }}</td>
                                <td>{{ productItem.quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </section>
                <div class="tabs mb-4">
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

                <section v-if="fields.type === 'entrada' || fields.type === 'salida'" class="db-panel">
                    <h3 class="db-panel__title">
                        Agregar {{ fields.type }}
                    </h3>
                    <div v-for="index in fields.product_count" :key="'entrada-'+index" class="mb-4 product-row">
                        <button type="button" class="btn btn--danger btn--sm mt-4 btn-delete " v-if="index >= 2"  @click="quitarProducto(index)">
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
                                    <search-select-field
                                        :name="'inventory' + index + '_product_id'"
                                        v-model="fields['inventory' + index + '_product_id']"
                                        :options="templatesOptions"
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
                                        maxlength="80"
                                        step="any"

                                    />
                                    <field-errors :name="'inventory' + index + '_quantity'"></field-errors>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn--sm btn--primary mb-2" type="button" @click="fields.product_count++">Agregar productos a {{ fields.type == 'entrada' ? 'camara de refrigeración' : 'piso' }}</button>
                </section>
                <div v-if="fields.type === 'entrada' || fields.type === 'salida'" class="text-center p-8">
                    <form-button class="btn--primary btn--wide">
                        Guardar
                    </form-button>
                </div>

                <div class="tab-content mt-3">
                    <div v-show="fields.type==='entrada'">
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
                                    <tr v-for="productItem in movementsEntradas" class="table-resource__row" :key="productItem.id">
                                        <td>{{ formatearFecha(productItem.date) }}</td>
                                        <td>{{ productItem.inventory.product.name }}</td>
                                        <td>{{ productItem.quantity }}</td>
                                        <td>
                                            <button
                                                class="btn btn--sm btn--primary"
                                                type="button"
                                                @click="editarMovimiento(productItem, 'entrada')"
                                            >
                                                Editar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </section>
                    </div>

                    <!-- SALIDAS -->
                    <div v-show="fields.type==='salida'">
                        <section class="db-panel">
                            <h3 class="db-panel__title">
                                Historial de producto en piso
                            </h3>
                            <table class="table size-caption mx-auto md:table--responsive">
                                <thead>
                                    <tr class="table-resource__headings">
                                        <th>Fecha</th>
                                        <th>Nombre</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="productItem in movementsSalidas" class="table-resource__row" :key="productItem.id">
                                        <td>{{ formatearFecha(productItem.date) }}</td>
                                        <td>{{ productItem.inventory.product.name }}</td>
                                        <td>{{ productItem.quantity }}</td>
                                        <td>
                                            <button
                                                class="btn btn--sm btn--primary"
                                                type="button"
                                                @click="editarMovimiento(productItem, 'salida')"
                                            >
                                                Editar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </section>
                    </div>

                    <!-- RESUMEN -->
                    <div v-show="fields.type==='resumen'">
                        <div class="md:row mb-4">
                            <div class="md:col">
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
                    </div>
                </div>
            </div>
        </form> 
        <div v-if="editandoMovimiento" class="modal">
            <div class="modal-content">
                <h3>Editar {{ movimientoEditando.type }}</h3>

                <div v-if="movimientoEditando.type === 'salida'" class="form-control">
                    <label>Nota / Venta ID</label>
                    <text-field
                        name="sale_id"
                        v-model="movimientoEditando.sale_id"
                        :initial="movimientoEditando.sale_id"
                    />
                </div>
                <div v-if="movimientoEditando.type === 'salida'" class="form-control">
                    <label>Nombre</label>
                    <text-field
                        name="name"
                        v-model="movimientoEditando.name"
                        :initial="movimientoEditando.name"
                    />
                </div>
                <div class="form-control">
                    <label>Fecha</label>
                    <date-field
                        v-model="movimientoEditando.date"
                        name="edit_date"
                        :initial="movimientoEditando.date"
                    />
                </div>

                <div class="form-control">
                    <label>Producto</label>
                    <search-select-field
                        name="template"
                        v-model="movimientoEditando.template_id"
                        :options="templatesOptions"
                        :initial="movimientoEditando.template_id"
                    />
                </div>

                <div class="form-control">
                    <label>Cantidad</label>
                    <text-field
                        name="quantity"
                        v-model="movimientoEditando.quantity"
                        type="number"
                        :initial="movimientoEditando.quantity"
                        step="any"
                    />
                </div>

                <div class="text-right mt-4">
                    <a class="btn btn--secondary mr-2" @click="cancelarEdicion">
                        Cancelar
                    </a>
                    <a class="btn btn--primary" @click="guardarEdicionMovimiento">
                        Guardar cambios
                    </a>
                </div>
            </div>
        </div>
  
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
            inventory: {},
            movementsEntradas: {},
            movementsSalidas: {},
            fields: {
                product_count: 1,
                type: 'resumen',
            },
            editandoMovimiento: false,
            movimientoEditando: {},
        };
    },
    computed: {
        templatesOptions() {
            return this.products.reduce((obj, t) => {
                obj[t.id] = t.product_name || t.name;
                return obj;
            }, {});
        },
        resumen() {
            const resumen = {};

            // obtener todos los nombres de productos
            const productos = Object.values(this.inventory).map(i => i.product.name);

            productos.forEach(p => {
                const inv = Object.values(this.inventory).find(i => i.product.name === p);
                const stock = inv ? Number(inv.quantity || 0) : 0;

                // entradas
                const entradas = Object.values(this.movementsEntradas || {}).reduce((acc, m) =>{ 
                    return m.inventory.product.name === p
                        ? acc + Number(m.quantity || 0)
                        : acc;
                }, 0);

                // salidas
                const salidasArray = Object.values(this.movementsSalidas || {}).filter(
                    m => m.inventory.product.name === p
                );
                const salidas = salidasArray.reduce((acc, m) => acc + Number(m.quantity || 0), 0);

                let promedio = 0;
                let sugerencia = 0;

                if (salidasArray.length > 0) {
                    // Fechas mín y máx de las salidas
                    const fechas = salidasArray.map(m => new Date(m.date));
                    const minFecha = new Date(Math.min(...fechas));
                    const maxFecha = new Date(Math.max(...fechas));

                    // diferencia en días (si es 0, lo dejamos en 1 para no dividir por 0)
                    const diffMs = maxFecha - minFecha;
                    const dias = diffMs > 0 ? Math.ceil(diffMs / (1000 * 60 * 60 * 24)) : 1;

                    // promedio real por día
                    promedio = (salidas / dias).toFixed(1);

                    // sugerencia para cubrir el mismo rango de días hacia el futuro
                    sugerencia = Math.max(0, Math.ceil(promedio * dias - stock));
                }

                resumen[p] = { stock, entradas, salidas, promedio, sugerencia };
            });

            return resumen;
        } 
    },
    methods: {

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
            this.inventory = Array.isArray(response.data.inventory) ? [...response.data.inventory] : [];
            this.movementsEntradas = Array.isArray(response.data.movementsEntradas) ? [...response.data.movementsEntradas] : [];
            this.movementsSalidas = Array.isArray(response.data.movementsSalidas) ? [...response.data.movementsSalidas] : [];
        },

        quitarProducto(index) {
            // Elimina los campos específicos del producto
            if(this.fields.type == 'salida') {
                this.$delete(this.fields, 'inventory' + index + '_sale_id');
                this.$delete(this.fields, 'inventory' + index + '_name');

            }

            this.$delete(this.fields, 'inventory' + index + '_date');
            this.$delete(this.fields, 'inventory' + index + '_product_id');
            this.$delete(this.fields, 'inventory' + index + '_quantity');

            // Reduce product_count solo si es mayor a 1
            if (this.fields.product_count > 1) {
                this.fields.product_count--;
            }
        },

        editarMovimiento(item, type) {
            this.editandoMovimiento = true;
            this.movimientoEditando = {
                id: item.id,
                type: type, 
                date: item.date,
                quantity: item.quantity,
                product_id: item.inventory.product_id,
                name: type === 'salida' ? item.name || '' : null,
                sale_id: type === 'salida' ? item.sale_id || '' : null
            };
        },

        cancelarEdicion() {
            this.editandoMovimiento = false;
            this.movimientoEditando = {};
        },

        async guardarEdicionMovimiento() {
            try {
                // Construir payload base
                const payload = {
                    date: this.movimientoEditando.date,
                    quantity: this.movimientoEditando.quantity,
                    template_id: this.movimientoEditando.product_id,
                };

                // Agregar sale_id si es salida
                if (this.movimientoEditando.type === 'salida') {
                    payload.sale_id = this.movimientoEditando.sale_id || '';
                    payload.name = this.movimientoEditando.name || '';
                }

                // Llamada al backend
                const response = await window.axios.post(
                    `inventario-movimiento/${this.movimientoEditando.id}/actualizar`,
                    payload
                );

                const updatedMovement = response.data.movement;

                // Selecciona la lista correcta según tipo
                const listName = this.movimientoEditando.type === 'entrada'
                    ? 'movementsEntradas'
                    : 'movementsSalidas';

                // Buscar y actualizar el movimiento en la lista
                const index = this[listName].findIndex(m => m.id === updatedMovement.id);
                if (index !== -1) {
                    this.$set(this[listName], index, {
                        ...this[listName][index],
                        ...updatedMovement,
                    });
                }

                // Cerrar modal
                this.editandoMovimiento = false;
                this.movimientoEditando = {};

            } catch (error) {
                console.error("Error al actualizar el movimiento:", error);
                alert("Hubo un error al guardar los cambios.");
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
