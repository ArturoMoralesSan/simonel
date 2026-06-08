<template>
  <form>
    <div class="mb-4">
      <div class="box box--lg bg-white b-1 rounded relative">
        <!-- Nombre -->
        <div class="md:row mb-4">
          <div class="md:col">
            <div class="form-control">
              <label for="product_name">Nombre del producto</label>
              <text-field 
                name="product_name" 
                v-model="fields.product_name" 
                maxlength="80" 
                :initial="template ? template.product_name : ''"
              />
              <field-errors name="product_name"></field-errors>
            </div>
          </div>
        </div>

        <!-- Precios -->
        <div class="md:row mb-2">
          <div class="md:col-1/2">
            <div class="form-control">
              <label for="base_price">Costo por unidad</label>
              <text-field 
                name="base_price" 
                v-model="fields.base_price" 
                maxlength="80" 
                :initial="template ? template.base_price : ''"
              />
              <field-errors name="base_price"></field-errors>
            </div>
          </div>
          <div class="md:col-1/2">
            <div class="form-control">
              <label for="profit_percentage">Utilidad por unidad (%)</label>
              <text-field 
                name="profit_percentage" 
                v-model="fields.profit_percentage" 
                maxlength="6" 
                :initial="template ? template.profit_percentage : ''"
              />
              <field-errors name="profit_percentage"></field-errors>
            </div>
          </div>
        </div>
        <div class="md:row mb-4">
            <div class="md:col-1/2">
                <div class="form-control">
                    <label for="has_inventory">¿Deseas generar inventario?</label>
                    <select-field
                        name="has_inventory"
                        v-model="fields.has_inventory"
                        :options="{ 1: 'Sí', 0: 'No' }"
                        :initial="template ? template.has_inventory : 0"
                    />
                    <field-errors name="has_inventory"></field-errors>
                </div>
            </div>
        </div>


        <!-- Importe -->
        <div class="md:row mb-2">
          <div class="md:col">
            <div class="sale-price-wrapper">
              <div class="sale-price-container">
                <label class="sale-price-label">Importe</label>
                <p class="sale-price-value">${{ salePrice.toFixed(4) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Botón -->
    <div class="text-center pt-8">
      <form-button class="btn--primary btn--wide">
        Enviar
      </form-button>
    </div>
  </form>
</template>

<script>
import BaseForm from '../base/BaseForm.vue';

export default {
  extends: BaseForm,

  props: {
    template: { 
        type: [Object, Array], 
        required: false 
    },
    mode: { 
        type: [String], 
        required: true 
    }
  },

  data() {
    return {
      fields: {
        product_name: '',
        base_price: '',
        profit_percentage: '',
        mode:this.mode,
        has_inventory: 0
      }
    };
  },

  computed: {
    salePrice() {
      const base = parseFloat(this.fields.base_price) || 0;
      const profit = parseFloat(this.fields.profit_percentage) || 0;
      return base + (base * profit / 100);
    }
  }
};
</script>

<style scoped>
.sale-price-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}
.sale-price-container {
  text-align: center; 
  padding: 1.5rem;
}
.sale-price-label {
  display: block;
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.sale-price-value {
  font-size: 24px;
  font-weight: bold;
  color: #2c7a7b;
}
</style>
