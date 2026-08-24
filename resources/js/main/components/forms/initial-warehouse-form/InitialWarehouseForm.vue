<template>

    <form>

        <div class="mb-4">

            <ItemForm
                v-for="i in fields.item_count"
                :key="i"
                :index="i"
                :min-item="minItem"
                :materials="materials"
                :products="products"
                :warehouses="warehouses"
                :fields="fields"
                :errors="errors"
                @remove="removeItems"
            >
            </ItemForm>


            <p class="pt-4">

                <button
                    v-if="fields.item_count < item"
                    class="btn btn--light mr-4"
                    type="button"
                    @click="fields.item_count++"
                >

                    <img
                        class="mr-1 align-top"
                        :src="$root.path + '/img/svg/plus-circle-primary.svg'"
                        alt=""
                        width="20px"
                    >

                    <span class="align-top">
                        Agregar más elementos
                    </span>

                </button>


                <span v-if="item > 1">

                    Puedes registrar un máximo de
                    {{ item }} elementos.

                </span>


                <span v-else>

                    Puedes registrar únicamente un elemento.

                </span>

            </p>

        </div>


        <!-- RESUMEN -->

        <div
            v-if="fields.item_count > 0"
            class="inventory-summary"
        >

            <div class="inventory-summary__row">

                <span>
                    Total del inventario inicial:
                </span>

                <strong>
                    ${{ totalGeneral.toFixed(2) }}
                </strong>

            </div>

        </div>


        <!-- BOTÓN -->

        <div class="text-center pt-8">

            <form-button class="btn--primary btn--wide">

                Guardar inventario inicial

            </form-button>

        </div>

    </form>

</template>


<script>

import BaseForm from '../base/BaseForm.vue';

import ItemForm from './ItemForm.vue';


export default {

    extends: BaseForm,


    components: {

        ItemForm

    },


    props: {

        /*
        |--------------------------------------------------------------------------
        | Cantidad máxima de elementos
        |--------------------------------------------------------------------------
        */

        item: {

            required: true,

            type: Number

        },


        /*
        |--------------------------------------------------------------------------
        | Cantidad mínima de elementos
        |--------------------------------------------------------------------------
        */

        minItem: {

            required: true,

            type: Number

        },


        /*
        |--------------------------------------------------------------------------
        | Materias primas
        |--------------------------------------------------------------------------
        */

        materials: {

            required: true,

            type: [Array, Object]

        },


        /*
        |--------------------------------------------------------------------------
        | Productos terminados
        |--------------------------------------------------------------------------
        */

        products: {

            required: true,

            type: [Array, Object]

        },


        /*
        |--------------------------------------------------------------------------
        | Almacenes
        |--------------------------------------------------------------------------
        */

        warehouses: {

            required: true,

            type: [Array, Object]

        }

    },


    data() {

        return {

            fields: {

                item_count: this.minItem

            }

        };

    },

    computed: {

        totalGeneral() {

            let total = 0;

            for (
                let i = 1;
                i <= this.fields.item_count;
                i++
            ) {

                const quantity = Number(
                    this.fields[
                        'item' + i + '_quantity'
                    ] || 0
                );

                const publicPrice = Number(
                    this.fields[
                        'item' + i + '_public_price'
                    ] || 0
                );

                total += quantity * publicPrice;
            }

            return total;
        }

    },



    methods: {

        /*
        |--------------------------------------------------------------------------
        | Copiar campos de un elemento a otro
        |--------------------------------------------------------------------------
        */

        copyItemsFields(source, target) {

            const regex = new RegExp(

                '^item' + source + '_'

            );


            this.deleteItemsFields(target);


            for (let field in this.fields) {

                if (regex.test(field)) {

                    const newField = field.replace(

                        'item' + source + '_',

                        'item' + target + '_'

                    );


                    this.$set(

                        this.fields,

                        newField,

                        this.fields[field]

                    );

                }

            }

        },


        /*
        |--------------------------------------------------------------------------
        | Eliminar campos de un elemento
        |--------------------------------------------------------------------------
        */

        deleteItemsFields(index) {

            const regex = new RegExp(

                '^item' + index + '_'

            );


            for (let field in this.fields) {

                if (regex.test(field)) {

                    this.$delete(

                        this.fields,

                        field

                    );

                }

            }

        },


        /*
        |--------------------------------------------------------------------------
        | Eliminar elemento
        |--------------------------------------------------------------------------
        */

        removeItems(index) {

            /*
            |--------------------------------------------------------------------------
            | Movemos todos los elementos posteriores
            | una posición hacia arriba.
            |--------------------------------------------------------------------------
            */

            for (

                let i = 0;

                i < this.fields.item_count - index;

                i++

            ) {

                this.copyItemsFields(

                    index + i + 1,

                    index + i

                );

            }


            /*
            |--------------------------------------------------------------------------
            | Reducimos la cantidad.
            |--------------------------------------------------------------------------
            */

            this.fields.item_count--;


            /*
            |--------------------------------------------------------------------------
            | Eliminamos los campos que sobraron.
            |--------------------------------------------------------------------------
            */

            this.deleteItemsFields(

                this.fields.item_count + 1

            );

        }

    }

};

</script>


<style scoped>

.inventory-summary {

    margin-top: 20px;

    padding: 20px;

    background: #f8fafc;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

}


.inventory-summary__row {

    display: flex;

    justify-content: space-between;

    align-items: center;

    font-size: 18px;

}


.inventory-summary__row strong {

    font-size: 22px;

}

</style>
