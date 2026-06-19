<template>
    <form>
        <div class="row mb-4">
            <div class="col">
                <div class="form-control">
                    <label for="product_id">Producto</label>
                    <select-field 
                        name="product_id" 
                        v-model="fields.product_id" 
                        :options="products"
                         :initial="recipesData.manufactured_product_id ? recipesData.manufactured_product_id.toString() : ''"
                    >
                    </select-field>
                    <field-errors name="product_id"></field-errors>
                </div>
            </div>
            
        </div>
        <div class="row mb-4">
            <div class="col-1/2">
                <div class="form-control">
                    <label for="yield_quantity">Cantidad a producir</label>
                    <text-field 
                        class="" 
                        name="yield_quantity" 
                        v-model="fields.yield_quantity" 
                        maxlength="20" 
                        :initial="recipesData.yield_quantity ? recipesData.yield_quantity.toString() : ''"
                    ></text-field>
                    <field-errors name="yield_quantity"></field-errors>
                </div>
            </div>
            <div class="col-1/2">
                <div class="form-control">
                    <label for="unit">Unidad</label>
                    <text-field 
                        class="" 
                        name="unit" 
                        v-model="fields.unit" 
                        maxlength="20" 
                         :initial="recipesData.unit ? recipesData.unit.toString() : ''"
                    ></text-field>
                    <field-errors name="unit"></field-errors>
                </div>
            </div>
            
        </div>
        
        <div class="mb-4">
            <ItemForm v-for="i in fields.item_count" :key="i"
                :index="i"
                :min-item="minItem"
                :materials="materialsData"
                :assigned-materials="assignedMaterials"
                :errors="errors"
                :fields="fields"
                @removeP="removeItems"
            >
            </ItemForm>

            <p class="pt-4">
                <button v-if="fields.item_count < item"
                    class="btn btn--light mr-4"
                    type="button"
                    @click="fields.item_count++"
                >
                    <img class="mr-1 align-top"
                        :src="$root.path + '/img/svg/plus-circle-primary.svg'"
                        alt=""
                        width="20px"
                    >
                    <span class="align-top">Agregar más elementos</span>
                </button>

                <span v-if="item > 1 "> Puedes registrar a un máximo de {{ item }} elementos.</span>
                <span v-else> Puedes registrar únicamente un elemento.</span>
            </p>
        </div>
        <div class="md:row">
            <div class="md:col">
                <div class="form-control">
                    <label for="notes">Comentarios adicionales</label>
                    <text-area name="notes" rows="10" cols="50" v-model="fields.notes" maxlength="2000">{{ recipesData.notes  || '' }} </text-area>
                    <field-errors name="notes"></field-errors>

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
    import ItemForm from './ItemForm.vue';

    export default {
        extends: BaseForm,

        components: { ItemForm },
        props: {
            item: {
                required: true,
                type: Number
            },
            minItem: {
                required: true,
                type: Number
            },
            materialsData: {
                required: true,
                type: [Array, Object]
            },
            recipesData: {
                required: true,
                type: [Array, Object]
            },
            assignedMaterials: {
                required: true,
                type: Array
            },
            products: {
                required: true,
                type: [Array, Object]
            },
    

        },
        data() {
            return {
                inputdisabled : true,
                currentDate: null,
                firstTime: null,
                fields: {
                    item_count: this.minItem,
                    product_recipe_id : this.recipesData.id || null,
                }
            };
        },
        
        mounted() {
            if (this.assignedMaterials.length != 0) {
                this.fields.item_count = this.assignedMaterials.length || this.minItem;
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
             copyItemsFields(source, target) {
                const regex = new RegExp('^item' + source + '_');

                this.deleteItemsFields(target);

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
             deleteItemsFields(index) {
                const regex = new RegExp('^item' + index + '_');

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
             removeItems(index) {
                for (let i = 0; i < this.fields.item_count - index; i ++) {
                    this.copyItemsFields(index + i + 1, index + i);
                }

                this.fields.item_count--;

                this.deleteItemsFields(this.fields.item_count + 1);
            }
        }
    };
</script>
