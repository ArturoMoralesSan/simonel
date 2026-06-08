<template>
  <div>
    <!-- Tabs -->
    <div class="tabs mb-4">
      <button
        :class="['tab-btn', { active: mode === 'basic' }]"
        @click="mode = 'basic'"
      >
        Configuración Básica
      </button>
      <button
        :class="['tab-btn', { active: mode === 'advanced' }]"
        @click="mode = 'advanced'"
      >
        Configuración Avanzada
      </button>
    </div>

    <!-- Componentes según modo -->
    <basic-template-form
      v-if="mode === 'basic'"
      :action="action"
      :template="template"
      :mode="mode"
    />
    <advanced-template-form
      v-else
      :action="action"
      :template="template"
      :products="products"
      :cuts="cuts"
      :types="types"
      :mode="mode"
    />
  </div>
</template>

<script>
import BasicTemplateForm from './BasicTemplateForm.vue';
import AdvancedTemplateForm from './AdvancedTemplateForm.vue';

export default {
  components: {
    BasicTemplateForm,
    AdvancedTemplateForm
  },
  props: {
    action: {
        type:String,
        required:true
    },
    template: { 
        type: [Object, Array], 
        required: false 
    },
    products: { 
        type: [Object, Array], 
        required: true 
    },
    cuts: { 
        type: [Array, Object], 
        required: true 
    },
    types: { 
        type: [Array, Object], 
        required: true 
    },
  },
  data() {
    return {
      mode: 'basic' // por defecto abre en básico
    };
  }
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
</style>
