<template>

    <form>

        <section class="db-panel">

            <h3 class="db-panel__title">
                Datos Generales
            </h3>

            <div class="md:row mb-2">

                <div class="md:col-2/3">

                    <div class="form-control">

                        <label for="business_name">
                            Razón social
                        </label>

                        <text-field
                            name="business_name"
                            v-model="fields.business_name"
                            maxlength="80"
                            :initial="user.business_name || ''"
                        />

                        <field-errors name="business_name" />

                    </div>

                </div>

                <div class="md:col-1/3">

                    <div class="form-control">

                        <label for="rfc">
                            RFC
                        </label>

                        <text-field
                            name="rfc"
                            v-model="fields.rfc"
                            maxlength="13"
                            :initial="user.rfc || ''"
                        />

                        <field-errors name="rfc" />

                    </div>

                </div>

            </div>

            <div class="md:row mb-2">

                <div class="md:col">

                    <div class="form-control">

                        <label for="trade_name">
                            Nombre comercial
                        </label>

                        <text-field
                            name="trade_name"
                            v-model="fields.trade_name"
                            maxlength="80"
                            :initial="user.trade_name || ''"
                        />

                        <field-errors name="trade_name" />

                    </div>

                </div>

            </div>

            <div class="md:row mb-2">

                <div class="md:col-1/2">

                    <div class="form-control">

                        <label for="tax_regime">
                            Regimen fiscal
                        </label>

                        <select-field
                            name="tax_regime"
                            v-model="fields.tax_regime"
                            :options="regimen"
                            :initial="user.tax_regime || ''"
                        />

                        <field-errors name="tax_regime" />

                    </div>

                </div>

                <div class="md:col-1/2">

                    <div class="form-control">

                        <label for="customer_type">
                            Tipo de cliente
                        </label>

                        <select-field
                            name="customer_type"
                            v-model="fields.customer_type"
                            :options="{
                                mayorista: 'Mayorista',
                                minorista: 'Minorista'
                            }"
                            :initial="user.customer_type || 'minorista'"
                        />

                        <field-errors name="customer_type" />

                    </div>

                </div>

            </div>

            <div class="md:row mb-2">

                <div class="md:col-1/2">

                    <div class="form-control">

                        <label for="phone">
                            Télefono
                        </label>

                        <text-field
                            name="phone"
                            v-model="fields.phone"
                            maxlength="80"
                            :initial="user.phone || ''"
                        />

                        <field-errors name="phone" />

                    </div>

                </div>

                <div class="md:col-1/2">

                    <div class="form-control">

                        <label for="email">
                            Correo electronico
                        </label>

                        <text-field
                            name="email"
                            v-model="fields.email"
                            maxlength="80"
                            :initial="user.email || ''"
                        />

                        <field-errors name="email" />

                    </div>

                </div>

            </div>

            <div class="row mb-2" v-if="isAdmin">

                <div class="col-1/2">

                    <div class="form-control">

                        <label for="seller_id">
                            Asignar vendedor
                        </label>

                        <select-field
                            name="seller_id"
                            v-model="fields.seller_id"
                            :options="sellers"
                            :initial="user.seller_id || ''"
                        />

                        <field-errors name="seller_id" />

                    </div>

                </div>

            </div>

        </section>


        <!-- DIRECCIÓN -->

        <section class="db-panel mb-16">

            <h3 class="db-panel__title">
                Dirección
            </h3>

            <div class="md:row mb-2">

                <div class="md:col-3/5">

                    <div class="form-control">

                        <label for="street">
                            Calle
                        </label>

                        <text-field
                            name="street"
                            v-model="fields.street"
                            maxlength="80"
                            :initial="user.street || ''"
                        />

                        <field-errors name="street" />

                    </div>

                </div>

                <div class="md:col-1/5">

                    <div class="form-control">

                        <label for="ext_number">
                            N° exterior
                        </label>

                        <text-field
                            name="ext_number"
                            v-model="fields.ext_number"
                            maxlength="80"
                            :initial="user.ext_number || ''"
                        />

                        <field-errors name="ext_number" />

                    </div>

                </div>

                <div class="md:col-1/5">

                    <div class="form-control">

                        <label for="int_number">
                            N° interior
                        </label>

                        <text-field
                            name="int_number"
                            v-model="fields.int_number"
                            maxlength="80"
                            :initial="user.int_number || ''"
                        />

                        <field-errors name="int_number" />

                    </div>

                </div>

            </div>

            <div class="md:row mb-2">

                <div class="md:col-1/2">

                    <div class="form-control">

                        <label for="between_streets">
                            Entre calle
                        </label>

                        <text-field
                            name="between_streets"
                            v-model="fields.between_streets"
                            maxlength="80"
                            :initial="user.between_streets || ''"
                        />

                        <field-errors name="between_streets" />

                    </div>

                </div>

                <div class="md:col-1/2">

                    <div class="form-control">

                        <label for="and_street">
                            Y calle
                        </label>

                        <text-field
                            name="and_street"
                            v-model="fields.and_street"
                            maxlength="80"
                            :initial="user.and_street || ''"
                        />

                        <field-errors name="and_street" />

                    </div>

                </div>

            </div>

            <div class="md:row mb-2">

                <div class="md:col-1/3">

                    <div class="form-control">

                        <label for="postal_code">
                            CP
                        </label>

                        <text-field
                            name="postal_code"
                            v-model="fields.postal_code"
                            maxlength="5"
                            :initial="user.postal_code || ''"
                        />

                        <field-errors name="postal_code" />

                    </div>

                </div>

                <div class="md:col-1/3">

                    <div class="form-control">

                        <label for="country">
                            País
                        </label>

                        <text-field
                            name="country"
                            v-model="fields.country"
                            maxlength="80"
                            :initial="user.country || ''"
                        />

                        <field-errors name="country" />

                    </div>

                </div>

                <div class="md:col-1/3">

                    <div class="form-control">

                        <label for="state">
                            Estado
                        </label>

                        <text-field
                            name="state"
                            v-model="fields.state"
                            maxlength="80"
                            :initial="user.state || ''"
                        />

                        <field-errors name="state" />

                    </div>

                </div>

            </div>

            <div class="md:row mb-2">

                <div class="md:col-1/3">

                    <div class="form-control">

                        <label for="municipality">
                            Municipio
                        </label>

                        <text-field
                            name="municipality"
                            v-model="fields.municipality"
                            maxlength="80"
                            :initial="user.municipality || ''"
                        />

                        <field-errors name="municipality" />

                    </div>

                </div>

                <div class="md:col-1/3">

                    <div class="form-control">

                        <label for="population">
                            Población
                        </label>

                        <text-field
                            name="population"
                            v-model="fields.population"
                            maxlength="80"
                            :initial="user.population || ''"
                        />

                        <field-errors name="population" />

                    </div>

                </div>

                <div class="md:col-1/3">

                    <div class="form-control">

                        <label for="colony">
                            Colonia
                        </label>

                        <select-field
                            name="colony"
                            v-model="fields.colony"
                            :options="colonies"
                            :initial="user.colony || ''"
                        />

                        <field-errors name="colony" />

                    </div>

                </div>

            </div>

        </section>


        <!-- CRÉDITO -->

        <section class="db-panel" v-if="isAdmin">

            <h3 class="db-panel__title">
                Crédito
            </h3>

            <div class="md:row mb-2">

                <div class="col">

                    <div class="form-control">

                        <label for="credit">
                            Credito
                        </label>

                        <select-field
                            name="credit"
                            v-model="fields.credit"
                            :options="{
                                1: 'Sí',
                                0: 'No'
                            }"
                            :initial="user.credit_setting?.enabled || 0"
                        />

                        <field-errors name="credit" />

                    </div>

                </div>

            </div>

            <div class="row" v-if="isCredit">

                <div class="md:col-1/2">

                    <div class="form-control">

                        <label for="credit_limit">
                            Límite de crédito
                            <span class="description">$</span>
                        </label>

                        <text-field
                            name="credit_limit"
                            v-model="fields.credit_limit"
                            maxlength="10"
                            :initial="String(
                                user.credit_setting?.credit_limit ?? ''
                            )"
                        />

                        <field-errors name="credit_limit" />

                    </div>

                </div>

                <div class="md:col-1/2">

                    <div class="form-control">

                        <label for="credit_days">
                            Periodo en días del crédito
                        </label>

                        <text-field
                            name="credit_days"
                            v-model="fields.credit_days"
                            maxlength="5"
                            :initial="String(
                                user.credit_setting?.credit_days ?? ''
                            )"
                        />

                        <field-errors name="credit_days" />

                    </div>

                </div>

            </div>

        </section>


        <!-- PRECIOS PREFERENCIALES -->

        <section class="db-panel mb-16">

            <h3 class="db-panel__title">
                Precios preferenciales
            </h3>

            <div class="mb-4">

                <CustomerProductPriceItem
                    v-for="i in fields.price_count"
                    :key="i"
                    :index="i"
                    :products="products"
                    :assignedPrices="assignedPrices"
                    :fields="fields"
                    :errors="errors"
                    @remove="removePrices"
                />

                <p class="pt-4">

                    <button
                        class="btn btn--light mr-4"
                        type="button"
                        @click="addPrice"
                    >

                        <img
                            class="mr-1 align-top"
                            :src="$root.path + '/img/svg/plus-circle-primary.svg'"
                            alt=""
                            width="20px"
                        >

                        <span class="align-top">
                            Agregar producto
                        </span>

                    </button>

                </p>

            </div>

        </section>


        <div class="text-center">

            <button
                class="btn"
                :class="Object.keys(user).length > 0
                    ? 'btn--blue--dashboard'
                    : 'btn--success'"
            >

                {{
                    Object.keys(user).length > 0
                        ? 'Actualizar'
                        : 'Crear'
                }}

            </button>

        </div>

    </form>

</template>


<script>

import BaseForm from '../../main/components/forms/base/BaseForm.vue';
import CustomerProductPriceItem from './CustomerProductPriceItem.vue';

export default {

    extends: BaseForm,

    components: {
        CustomerProductPriceItem
    },

    props: {

        sellers: {
            required: true,
            type: [Array, Object]
        },

        regimen: {
            required: true,
            type: [Array, Object]
        },

        products: {
            required: true,
            type: [Array, Object]
        },

        isAdmin: {
            type: Boolean,
            default: false
        },

        user: {
            required: true,
            type: [Array, Object]
        }

    },

    data() {

        const assignedPrices =
            Array.isArray(this.user.product_prices)
                ? this.user.product_prices
                : [];

        return {

            fields: {

                user_id: this.user.user_id || null,

                customer_id: this.user.id || null,

                postal_code: this.user.postal_code || '',

                country: this.user.country || '',

                state: this.user.state || '',

                municipality: this.user.municipality || '',

                colony: this.user.colony || '',

                price_count: assignedPrices.length

            },

            assignedPrices,

            colonies: []

        };

    },

    computed: {

        isCredit() {

            return this.fields.credit == 1 ||
                this.fields.credit === '1';

        }

    },

    mounted() {

        if (this.assignedPrices.length != 0) {

            this.fields.price_count =
                this.assignedPrices.length;

        }

    },

    watch: {

        'fields.postal_code'(newPostalCode) {

            if (
                newPostalCode &&
                newPostalCode.length === 5
            ) {

                this.fetchAddress(newPostalCode);

            }

        }

    },

    methods: {

        addPrice() {

            this.fields.price_count++;

            const position =
                this.fields.price_count;

            this.$set(
                this.fields,
                `price${position}_product_id`,
                ''
            );

            this.$set(
                this.fields,
                `price${position}_price`,
                ''
            );

        },

        async fetchAddress(postalCode) {

            try {

                const response =
                    await window.axios.get(
                        '/api/postal-code',
                        {
                            params: {
                                cp: postalCode
                            }
                        }
                    );

                if (
                    response.status === 200 &&
                    response.data.success
                ) {

                    const data = response.data;

                    this.fields.country = 'México';

                    this.fields.state =
                        data.estado;

                    this.fields.municipality =
                        data.municipio;

                    this.fields.population =
                        data.ciudad;

                    this.fields.zone =
                        data.zona;

                    this.colonies =
                        data.colonias.map(
                            colonia => colonia
                        );

                    if (this.colonies.length) {

                        this.fields.colony =
                            this.colonies[0].value;

                    }

                }

            } catch (error) {

                console.error(
                    'Error al obtener los datos del código postal:',
                    error
                );

            }

        },

        removePrices(index) {

            for (
                let i = index;
                i < this.fields.price_count;
                i++
            ) {

                const currentPosition = i + 1;

                const previousPosition = i;

                const currentProduct =
                    `price${currentPosition}_product_id`;

                const currentPrice =
                    `price${currentPosition}_price`;

                const previousProduct =
                    `price${previousPosition}_product_id`;

                const previousPrice =
                    `price${previousPosition}_price`;

                this.$set(
                    this.fields,
                    previousProduct,
                    this.fields[currentProduct] || ''
                );

                this.$set(
                    this.fields,
                    previousPrice,
                    this.fields[currentPrice] || ''
                );

            }

            const lastPosition =
                this.fields.price_count;

            this.$delete(
                this.fields,
                `price${lastPosition}_product_id`
            );

            this.$delete(
                this.fields,
                `price${lastPosition}_price`
            );

            this.fields.price_count--;

            this.assignedPrices.splice(
                index - 1,
                1
            );

        }

    }

};

</script>
