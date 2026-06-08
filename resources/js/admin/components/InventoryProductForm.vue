<template>
    <div>
        <form>
            <div>
                <section class="db-panel">
                    <h3 class="db-panel__title">
                        Inventario de {{ inventory.product.name }}
                    </h3>
                    <table class="table size-caption mx-auto md:table--responsive">
                        <thead>
                            <tr class="table-resource__headings">
                                <th>Presentación</th>
                                <th>Medida</th>
                                <th>Etiqueta</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr  class="table-resource__row"">
                                <td>{{ inventory.product.cut.name }}</td>
                                <td>{{ inventory.product.cut.measure }}</td>
                                <td>{{ inventory.tag }}</td>
                                <td>{{ inventory.quantity }}</td>
                            </tr>
                        </tbody>
                    </table>
                </section>
                <div class="tabs mb-4">
                <a
                    :class="['btn tab-btn', { active: fields.type === 'entrada' }]"
                    @click="fields.type = 'entrada'"
                >
                    Entradas
                </a>
                <a
                    :class="['btn tab-btn', { active: fields.type === 'salida' }]"
                    @click="fields.type = 'salida'"
                >
                    Salidas
                </a>
                <a
                    :class="['btn tab-btn', { active: fields.type === 'resumen' }]"
                    @click="fields.type = 'resumen'"
                >
                    Resumen
                </a>
                </div>

                <div class="tab-content mt-3">
                    <div v-show="fields.type==='entrada'">
                        <section class="db-panel">
                            <h3 class="db-panel__title">
                                Historial de entradas
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
                                    <tr v-for="productItem in inputmovements" class="table-resource__row" :key="productItem.id">
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
                                Historial de salidas
                            </h3>
                            <table class="table size-caption mx-auto md:table--responsive">
                                <thead>
                                    <tr class="table-resource__headings">
                                        <th>Fecha</th>
                                        <th>Nota</th>
                                        <th>Nombre</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="productItem in outputmovements" class="table-resource__row" :key="productItem.id">
                                        <td>{{ formatearFecha(productItem.date) }}</td>
                                        <td>{{ productItem.sale_id }}</td>
                                        <td>{{ productItem.name }}</td>
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

                                    <h3 class="db-panel__title">
                                        Resumen Inteligente de Inventario
                                    </h3>

                                    <table class="table size-caption mx-auto md:table--responsive">

                                        <thead>
                                            <tr class="table-resource__headings">

                                                <th>Producto</th>
                                                <th>Stock actual</th>
                                                <th>Stock mínimo</th>
                                                <th>Total entradas</th>
                                                <th>Total salidas</th>
                                                <th>Promedio salida</th>
                                                <th>Días restantes</th>
                                                <th>Rotación</th>
                                                <th>Estado</th>
                                                <th>Sugerencia</th>
                                                <th>Producción sugerida</th>

                                            </tr>
                                        </thead>

                                        <tbody>

                                            <tr
                                                v-for="(producto, nombre) in resumen"
                                                :key="nombre"
                                                class="table-resource__row"
                                            >

                                                <td>
                                                    {{ nombre }}
                                                </td>

                                                <td>
                                                    {{ producto.stock }}
                                                </td>

                                                <td>
                                                    {{ producto.stockMinimo }}
                                                </td>

                                                <td>
                                                    {{ producto.entradas }}
                                                </td>

                                                <td>
                                                    {{ producto.salidas }}
                                                </td>

                                                <td>
                                                    {{ producto.promedio }}
                                                </td>

                                                <td>
                                                    {{ producto.diasRestantes }}
                                                </td>

                                                <td>

                                                    <span
                                                        :class="{
                                                            'text-red': producto.nivelRotacion === 'Alta',
                                                            'text-yellow': producto.nivelRotacion === 'Media',
                                                            'text-green': producto.nivelRotacion === 'Baja',
                                                        }"
                                                    >
                                                        {{ producto.nivelRotacion }}
                                                    </span>

                                                </td>

                                                <td>

                                                    <span
                                                        :class="{
                                                            'text-red': producto.estado === 'critico',
                                                            'text-yellow': producto.estado === 'alto_consumo',
                                                            'text-green': producto.estado === 'normal',
                                                            'text-blue': producto.estado === 'sobrestock',
                                                        }"
                                                    >
                                                        {{ producto.estado }}
                                                    </span>

                                                </td>

                                                <td>
                                                    {{ producto.sugerencia }}
                                                </td>

                                                <td>
                                                    {{ producto.cantidadSugerida }}
                                                </td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </section>

                            </div>

                        </div>

                    </div>
                </div>

                <section v-if="fields.type === 'entrada' || fields.type === 'salida'" class="db-panel">
                    <h3 class="db-panel__title">
                        Agregar {{ fields.type }}
                    </h3>
                    <div class="mb-4 product-row">
                        <div v-if="fields.type === 'salida'" class="md:row mb-2">
                            <div class="md:col">
                                <div class="form-control">
                                    <label>Nota / Venta ID</label>
                                    <text-field
                                        :name="'inventory_sale_id'"
                                        v-model="fields['inventory_sale_id']"
                                        maxlength="80"
                                    />
                                    <field-errors :name="'inventory_sale_id'"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div v-if="fields.type === 'salida'" class="md:row mb-2">
                            <div class="md:col">
                                <div class="form-control">
                                    <label>Nombre</label>
                                    <text-field
                                        :name="'inventory_name'"
                                        v-model="fields['inventory_name']"
                                        maxlength="120"
                                    />
                                    <field-errors :name="'inventory_name'"></field-errors>
                                </div>
                            </div>
                        </div>
                        <div class="md:row">
                            <div class="md:col-1/2"> 
                                <div class="form-control">
                                    <label>Fecha de inicio</label>
                                    <date-field 
                                        :name="'inventory_date'"
                                        v-model="fields['inventory_date']"
                                    />
                                    <field-errors :name="'inventory_date'"></field-errors>
                                </div>
                            </div>
                            
                            <div class="md:col-1/2"> 
                                <div class="form-control">
                                    <label>Cantidad</label>
                                    <text-field 
                                        :name="'inventory_quantity'"
                                        v-model="fields['inventory_quantity']"
                                        type="number"
                                        maxlength="80"
                                        step="any"

                                    />
                                    <field-errors :name="'inventory_quantity'"></field-errors>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <div v-if="fields.type === 'entrada' || fields.type === 'salida'" class="text-center p-8">
                    <form-button class="btn--primary btn--wide">
                        Guardar
                    </form-button>
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
        inventory: { required: true, type: [Array, Object] },
        inputmovements: { required: true, type: [Array, Object] },
        outputmovements: { required: true, type: [Array, Object] },
    },
    data() {
        return {
            productoNombre: '',
            fields: {
                product_id: this.inventory.product_id,
                type: 'resumen',
            },
            editandoMovimiento: false,
            movimientoEditando: {},
        };
    },
    computed: {

        resumen() {
            const entradas = this.inputmovements.reduce((total, item) => {
                return total + Number(item.quantity);
            }, 0);

            const salidas = this.outputmovements.reduce((total, item) => {
                return total + Number(item.quantity);
            }, 0);

            const promedioSalidas =
                this.outputmovements.length > 0
                    ? (salidas / this.outputmovements.length)
                    : 0;

            const stock = Number(this.inventory.quantity);
            const stockMinimo = Number(this.inventory.quantity_min || 0);

            let diasRestantes = 0;

            if (promedioSalidas > 0) {
                diasRestantes = (stock / promedioSalidas).toFixed(1);
            }

    
            let nivelRotacion = 'Baja';

            if (promedioSalidas >= stock * 0.5) {
                nivelRotacion = 'Alta';
            } else if (promedioSalidas >= stock * 0.2) {
                nivelRotacion = 'Media';
            }

            
            let sugerencia = 'Stock saludable';
            let estado = 'normal';

            if (stock <= stockMinimo) {
                sugerencia = 'Reponer inmediatamente';
                estado = 'critico';
            }

            else if (promedioSalidas >= stock * 0.5) {
                sugerencia = 'Comprar pronto';
                estado = 'alto_consumo';
            }
            else if (stock > (salidas * 2) && salidas > 0) {

                sugerencia = 'Inventario alto';
                estado = 'sobrestock';
            }


            const cantidadSugerida = Math.ceil(promedioSalidas * 7);

            return {
                [this.inventory.product.name]: {
                    stock,
                    stockMinimo,
                    entradas,
                    salidas,
                    promedio: promedioSalidas.toFixed(2),
                    diasRestantes,
                    sugerencia,
                    estado,
                    nivelRotacion,
                    cantidadSugerida,
                }
            };
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

        editarMovimiento(item, type) {
            this.editandoMovimiento = true;
            this.movimientoEditando = {
                id: item.id,
                type: type, // 'entrada' o 'salida'
                date: item.date,
                quantity: item.quantity,
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

                const payload = {
                    date: this.movimientoEditando.date,
                    quantity: Number(this.movimientoEditando.quantity),
                };

                if (this.movimientoEditando.type === 'salida') {
                    payload.sale_id =
                        this.movimientoEditando.sale_id || '';
                    payload.name =
                        this.movimientoEditando.name || '';
                }

                const response = await window.axios.post(
                    `${window.location.origin}/admin/inventario-movimiento/${this.movimientoEditando.id}/actualizar`,
                    payload
                );

                const updatedMovement = response.data.movement;
                const updatedInventory = response.data.inventory;

        
                this.inventory.quantity = updatedInventory.quantity;
                this.inventory.total_value = updatedInventory.total_value;

                const list =
                    this.movimientoEditando.type === 'entrada'
                        ? this.inputmovements
                        : this.outputmovements;

                const index = list.findIndex(
                    item => item.id === updatedMovement.id
                );

                if (index !== -1) {

                    this.$set(list, index, {

                        ...list[index],

                        ...updatedMovement
                    });
                }

                this.editandoMovimiento = false;
                this.movimientoEditando = {};

            } catch (error) {
                console.error(error);
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.error
                ) {
                    alert(error.response.data.error);

                } else {
                    alert('Hubo un error al guardar.');
                }
            }
        }
    },
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
