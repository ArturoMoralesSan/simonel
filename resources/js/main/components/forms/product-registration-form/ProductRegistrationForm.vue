<template>
    <form>
        <div class="row mb-4">
            <div class="col">
                <div class="form-control">
                    <label for="user_id">Cliente</label>
                    <search-select-field name="user_id" v-model="fields.user_id" :options="users" :initial="sale.user_id || '' ">
                    </search-select-field>
                    <field-errors name="user_id"></field-errors>
                </div>
            </div>
        </div>
      
        <div class="mb-4">
            <ProductForm v-for="i in fields.products_count" :key="i"
                :index="i"
                :min-product="minProduct"
                :products="products"
                :assigned-products="assignedProducts"
                :cuts="cuts"
                :types="types"
                :errors="errors"
                :fields="fields"
                @remove="removeProducts"  
                @cloneProduct="copyProductFields"
            >
            </ProductForm>

            <p class="pt-4">
                <button v-if="fields.products_count < product"
                    class="btn btn--light mr-4"
                    type="button"
                    @click="fields.products_count++"
                >
                    <img class="mr-1 align-top"
                        :src="$root.path + '/img/svg/plus-circle-primary.svg'"
                        alt=""
                        width="20px"
                    >
                    <span class="align-top">Agregar productos</span>
                </button>

                <span v-if="product > 1 "> Puedes registrar a un máximo de {{ product }} productos.</span>
                <span v-else> Puedes registrar únicamente un producto.</span>
            </p>
        </div>
        <div class="md:row">
            <div class="md:col">
                <div class="form-control">
                    <label for="comment">Comentarios adicionales</label>
                    <text-area name="comment" rows="10" cols="50" v-model="fields.comment" maxlength="2000">{{ sale.comment  || '' }} </text-area>
                    <field-errors name="comment"></field-errors>

                </div>
            </div>
        
        </div>
        
        <div class="text-center pt-8">
            <form-button class="btn--primary btn--wide">
                Enviar
            </form-button>
        </div>
   </form>
</template>

<script>
    import BaseForm from '../base/BaseForm.vue';
    import ProductForm from './ProductForm.vue';

    export default {
        extends: BaseForm,

        components: { ProductForm },
        props: {
            product: {
                required: true,
                type: Number
            },
            minProduct: {
                required: true,
                type: Number
            },
            products: {
                required: true,
                type: [Array, Object]
            },
            assignedProducts: {
                required: true,
                type: Array
            },

            users: {
                required: true,
                type: [Array, Object]
            },
            types: {
                required: true,
                type: [Array, Object]
            },
            cuts: {
                required: true,
                type: [Array, Object]
            },
            
            sale: {
                required: true,
                type: [Object, Array]
            },

        },
        data() {
            return {
                inputdisabled : true,
                currentDate: null,
                firstTime: null,
                fields: {
                    products_count: this.minProduct,
                    sale_id : this.sale.id || null,
                },
                cloning: false // 🚀 Nueva bandera de clonación

            };
        },
        
        mounted() {    
            if (this.assignedProducts.length != 0) {
                this.fields.products_count = this.assignedProducts.length;
            }

        },
        watch: {
            firstTime: function(val) {
                this.fields._method = val === false ? 'patch' : 'post';
            }
        },

        methods: {
            

 
            /**
             * Copy all author's fields from one card to another.
             *
             * @param {Integer} source
             * @param {Integer} target
             */
             copyProductFields(source, target) {
                this.cloning = true; // 🚀 Activamos la bandera de clonación

                const regex = new RegExp('^product' + source + '_');
                this.deleteProductFields(target);

                for (let field in this.fields) {
                    if (regex.test(field)) {
                        this.$set(this.fields, field.replace(source, target), this.fields[field]);
                    }
                }

                // Primero asignamos el type_id
                this.$set(this.fields, 'product' + target + '_type_id', this.fields['product' + source + '_type_id']);

                // Esperamos un momento antes de asignar product_id
                this.$nextTick(() => {
                    this.$set(this.fields, 'product' + target + '_product_id', this.fields['product' + source + '_product_id']);
                    this.cloning = false; // 🚀 Desactivamos la bandera
                });
            },




            /**
             * Delete all fields for the given author.
             *
             * @param {Integer} index
             */
            deleteProductFields(index) {
                const regex = new RegExp('^product' + index + '_');

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
             removeProducts(index) {
                for (let i = 0; i < this.fields.products_count - index; i ++) {
                    this.copyProductFields(index + i + 1, index + i);
                }

                this.$nextTick(() => {
                    this.fields.products_count--;

                    this.deleteProductFields(this.fields.products_count + 1);
                });
            }
            
        }
    };
</script>
