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

                utility: "0"
            }
        };
    },

    watch: {

        'fields.cut_id'(value) {

            const presentation = this.presentations.find(
                item => item.id == value
            );

            if (presentation) {
                this.fields.impresion_cost = presentation.cost;
            }
        },

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

                const vinil = parseNumber(this.fields.vinil_cost);
                const impresion = parseNumber(this.fields.impresion_cost);
                const indirecto = parseNumber(this.fields.indirect_cost);
                const utility = parseNumber(this.fields.utility);

                const subtotal = vinil + impresion + indirecto;
                const utilidadMonto = subtotal * (utility / 100);
                const costoVenta = subtotal + utilidadMonto;

                this.fields.subtotal = subtotal.toFixed(4);
                this.fields.costo_total = subtotal.toFixed(4);
                this.fields.costo_venta = costoVenta.toFixed(4);
            }
        }
    }
}
</script>