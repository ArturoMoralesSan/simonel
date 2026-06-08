<template>
    <form>
        <div class="row mb-4">
            <div class="col">
                <div class="form-control">
                    <label for="branch">Estado</label>
                    <select-field 
                        name="status" 
                        v-model="fields.status" 
                        :options="status"
                        :initial="(SaleData.status || '')"
                    >
                    </select-field>
                    <field-errors name="status"></field-errors>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <PaymentForm v-for="i in fields.payments_count" :key="i"
                :index="i"
                :min-payment="minPayment"
                :payments-data="paymentsData"
                :assigned-payments="assignedPayments"
                :errors="errors"
                :fields="fields"
                @removeP="removePayments"
            >
            </PaymentForm>

            <p class="pt-4">
                <button v-if="fields.payments_count < payment"
                    class="btn btn--light mr-4"
                    type="button"
                    @click="fields.payments_count++"
                >
                    <img class="mr-1 align-top"
                        :src="$root.path + '/img/svg/plus-circle-primary.svg'"
                        alt=""
                        width="20px"
                    >
                    <span class="align-top">Agregar metodo de pago</span>
                </button>

                <span v-if="payment > 1 "> Puedes registrar a un máximo de {{ payment }} métodos de pago.</span>
                <span v-else> Puedes registrar únicamente un método de pago.</span>
            </p>
        </div>
        <div class="md:row">
            <div class="md:col">
                <div class="form-control">
                    <label for="comment">Comentarios adicionales</label>
                    <text-area name="comment" rows="10" cols="50" v-model="fields.comment" maxlength="2000">{{ SaleData.comment  || '' }} </text-area>
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
    import PaymentForm from './PaymentForm.vue';

    export default {
        extends: BaseForm,

        components: { PaymentForm },
        props: {
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
            },
            assignedPayments: {
                required: true,
                type: Array
            },
            status: {
                required: true,
                type: [Array, Object]
            },
            
            SaleData: {
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
                    payments_count: this.minPayment,
                    sale_id : this.SaleData.id || null,
                }
            };
        },
        
        mounted() {
            if (this.assignedPayments.length != 0) {
                this.fields.payments_count = this.assignedPayments.length;
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
             copyPaymentFields(source, target) {
                const regex = new RegExp('^payment' + source + '_');

                this.deleteAuthorFields(target);

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
             deletePaymentFields(index) {
                const regex = new RegExp('^payment' + index + '_');

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
             removePayments(index) {
                for (let i = 0; i < this.fields.payments_count - index; i ++) {
                    this.copyPaymentFields(index + i + 1, index + i);
                }

                this.fields.payments_count--;

                this.deletePaymentFields(this.fields.payments_count + 1);
            }
        }
    };
</script>
