<script>

import BaseForm from '../../main/components/forms/base/BaseForm.vue';

export default {

    extends: BaseForm,

    data() {

        return {

            presentations: window.presentations || [],

            fields: {

                vinil_cost: "0",
                impresion_cost: "0",
                indirect_cost: "0",

                subtotal: "0",
                costo_total: "0",

                costo_venta: "0",

                // Utilidad en porcentaje
                utility: "0",

                // Utilidad en pesos
                utility_amount: "0"

            }

        };

    },

    watch: {

        fields: {

            deep: true,

            handler() {

                const parseNumber = (value) => {

                    if (typeof value === "string") {
                        value = value.replace(/,/g, "");
                    }

                    const num = parseFloat(value);

                    return isNaN(num) ? 0 : num;

                };


                const vinil = parseNumber(
                    this.fields.vinil_cost
                );

                const impresion = parseNumber(
                    this.fields.impresion_cost
                );

                const indirecto = parseNumber(
                    this.fields.indirect_cost
                );

                const costoVenta = parseNumber(
                    this.fields.costo_venta
                );


                /*
                |--------------------------------------------------------------------------
                | COSTO DE FABRICACIÓN
                |--------------------------------------------------------------------------
                */

                const subtotal =
                    vinil +
                    impresion +
                    indirecto;


                /*
                |--------------------------------------------------------------------------
                | UTILIDAD EN PESOS
                |--------------------------------------------------------------------------
                */

                const utilityAmount =
                    costoVenta - subtotal;


                /*
                |--------------------------------------------------------------------------
                | UTILIDAD EN PORCENTAJE
                |--------------------------------------------------------------------------
                */

                let utility = 0;

                if (subtotal > 0) {

                    utility =
                        (
                            utilityAmount /
                            subtotal
                        ) * 100;

                }


                /*
                |--------------------------------------------------------------------------
                | RESULTADOS
                |--------------------------------------------------------------------------
                */

                this.fields.subtotal =
                    subtotal.toFixed(4);

                this.fields.costo_total =
                    subtotal.toFixed(4);

                this.fields.utility_amount =
                    utilityAmount.toFixed(4);

                this.fields.utility =
                    utility.toFixed(2);

            }

        }

    }

}

</script>