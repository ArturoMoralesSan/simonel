<template>
    <form>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Datos Generales
            </h3>

            <div class="md:row mb-2">
                <div class="md:col-2/3">
                    <div class="form-control">
                        <label for="business_name">Razón social</label>
                        <text-field name="business_name" v-model="fields.business_name" maxlength="80" :initial="user.business_name || ''">
                        </text-field>
                        <field-errors name="business_name"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="rfc">RFC</label>
                        <text-field name="rfc" v-model="fields.rfc" maxlength="80" :initial="user.rfc || ''">
                        </text-field>
                        <field-errors name="rfc"></field-errors>
                    </div>
                </div>
            </div>
            <div class="md:row mb-2">
                <div class="md:col">
                    <div class="form-control">
                        <label for="trade_name">Nombre comercial</label>
                        <text-field name="trade_name" v-model="fields.trade_name" maxlength="80" :initial="user.trade_name || ''">
                        </text-field>
                        <field-errors name="trade_name"></field-errors>
                    </div>
                </div>
            </div>
            <div class="md:row mb-2">
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="tax_regime">Regimen fiscal</label>
                        <text-field name="tax_regime" v-model="fields.tax_regime" maxlength="80" :initial="user.tax_regime || ''">
                        </text-field>
                        <field-errors name="tax_regime"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="customer_type">Tipo de cliente</label>
                        <select-field
                            name="customer_type"
                            v-model="fields.customer_type"
                            :options="{
                                mayorista: 'Mayorista',
                                minorista: 'Minorista'
                            }"
                            :initial="user.customer_type || 'minorista'"
                        >
                        </select-field>
                        <field-errors name="customer_type"></field-errors>
                    </div>
                </div>
            </div>
            <div class="md:row mb-2">
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="phone">Télefono</label>
                        <text-field name="phone" v-model="fields.phone" maxlength="80" :initial="user.phone || ''">
                        </text-field>
                        <field-errors name="phone"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="email">Correo electronico</label>
                        <text-field name="email" v-model="fields.email" maxlength="80" :initial="user.email || ''">
                        </text-field>
                        <field-errors name="email"></field-errors>
                    </div>
                </div>
            </div>

            
            <!-- <div class="md:row mb-2">
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="status">Estado</label>
                        <select-field name="status" v-model="fields.status" :options="status" :initial="sale.status || '' ">
                        </select-field>
                        <field-errors name="status"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="is_paid">Pagado</label>
                        <select-field name="is_paid" v-model="fields.is_paid" :options="paid" :initial="sale.is_paid || '' ">
                        </select-field>
                        <field-errors name="is_paid"></field-errors>
                    </div>
                </div>
            </div> -->
        </section>

        <!-- Sección de Costo -->
        <section class="db-panel mb-16">
            <h3 class="db-panel__title">
                Dirección
            </h3>
            <div class="md:row mb-2">
                <div class="md:col-3/5">
                    <div class="form-control">
                        <label for="street">Calle</label>
                        <text-field name="street" v-model="fields.street" maxlength="80" :initial="user.street || ''">
                        </text-field>
                        <field-errors name="street"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/5">
                    <div class="form-control">
                        <label for="ext_number">N° exterior</label>
                        <text-field name="ext_number" v-model="fields.ext_number" maxlength="80" :initial="user.ext_number || ''">
                        </text-field>
                        <field-errors name="ext_number"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/5">
                    <div class="form-control">
                        <label for="int_number">N° interior</label>
                        <text-field name="int_number" v-model="fields.int_number" maxlength="80" :initial="user.int_number || ''">
                        </text-field>
                        <field-errors name="int_number"></field-errors>
                    </div>
                </div>
            </div>
            <div class="md:row mb-2">
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="between_streets">Entre calle</label>
                        <text-field name="between_streets" v-model="fields.between_streets" maxlength="80" :initial="user.between_streets || ''">
                        </text-field>
                        <field-errors name="between_streets"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/2">
                    <div class="form-control">
                        <label for="and_street">Y calle</label>
                        <text-field name="and_street" v-model="fields.and_street" maxlength="80" :initial="user.and_street || ''">
                        </text-field>
                        <field-errors name="and_street"></field-errors>
                    </div>
                </div>
            </div>
            <div class="md:row mb-2">
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="postal_code">CP</label>
                        <text-field name="postal_code" v-model="fields.postal_code" maxlength="80" :initial="user.postal_code || ''">
                        </text-field>
                        <field-errors name="postal_code"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="country">País</label>
                        <text-field name="country" v-model="fields.country" maxlength="80" :initial="user.country || ''">
                        </text-field>
                        <field-errors name="country"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="state">Estado</label>
                        <text-field name="state" v-model="fields.state" maxlength="80" :initial="user.state || ''">
                        </text-field>
                        <field-errors name="state"></field-errors>
                    </div>
                </div>
            </div>
            <div class="md:row mb-2">
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="municipality">Municipio</label>
                        <text-field name="municipality" v-model="fields.municipality" maxlength="80" :initial="user.municipality || ''">
                        </text-field>
                        <field-errors name="municipality"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="population">Población</label>
                        <text-field name="population" v-model="fields.population" maxlength="80" :initial="user.population || ''">
                        </text-field>
                        <field-errors name="population"></field-errors>
                    </div>
                </div>
                <div class="md:col-1/3">
                    <div class="form-control">
                        <label for="colony">Colonia</label>
                        <select-field name="colony" v-model="fields.colony" :options="colonies" :initial="user.colony || ''">
                        </select-field>
                        <field-errors name="colony"></field-errors>
                    </div>
                </div>
            </div>
        </section>


        <div class="text-center">
            <button class="btn" :class="Object.keys(user).length > 0 ? 'btn--blue--dashboard' : 'btn--success'">
                {{ Object.keys(user).length > 0 ? 'Actualizar' : 'Crear' }}
            </button>
        </div>
    </form>
</template>

<script>
import BaseForm from '../../main/components/forms/base/BaseForm.vue';

export default {
    extends: BaseForm,
    props: {
        user: {
            required: true,
            type: [Array, Object]
        },
    },
    data() {
        return {
            fields: {
                user_id: this.user.user_id || null,
                customer_id: this.user.id || null,
                postal_code: '',
                country: '',
                state: '',
                municipality: '',
                colony: ''
            },
            colonies: [],
        }
    },
    watch: {
        'fields.postal_code'(newPostalCode) {
            if (newPostalCode.length === 5) {
                this.fetchAddress(newPostalCode);
            }
        }
    },
    methods: {
        async fetchAddress(postalCode) {

        try {

            const response = await window.axios.get('/api/postal-code', {
                params: { cp: postalCode }
            });

            if (response.status === 200 && response.data.success) {

                console.log(response.data);

                const data = response.data;

                this.fields.country = 'México';
                this.fields.state = data.estado;
                this.fields.municipality = data.municipio;
                this.fields.population = data.ciudad;
                this.fields.zone = data.zona;

                this.colonies = data.colonias.map(colonia => (colonia, colonia));

                if (this.colonies.length) {
                    this.fields.colony = this.colonies[0].value;

                }

            }

        } catch (error) {

            console.error(
                'Error al obtener los datos del código postal:',
                error
            );

        }

    }
    }
};
</script>