<template>
    <div class="filter-select" ref="dropdown">
        <div class="select-wrapper">
            <input
                ref="inputField"
                type="text"
                class="form-field"
                :class="{ 'form-field--invalid' : hasErrors }"
                v-model="filter"
                :placeholder="selectedText || 'Selecciona una opción...'"
                @input="filterOptions"
                @focus="onFocus"
            />
            <div class="dropdown-icon">▼</div>
        </div>
        <ul v-show="showOptions" class="options-list">
            <li 
                v-for="(option, key) in filteredOptions" 
                :key="key" 
                class="option-item" 
                @mousedown.prevent="selectOption(key, option)"
            >
                {{ option }}
            </li>
        </ul>
    </div>
</template>

<script>
import FormField from '../../../mixins/FormField.js';

export default {
    mixins: [FormField],

    props: {
        initial: {
            type: [Number, String],
            required: false,
            default: ''
        },
        options: {
            type: Object, // Usa objetos { id: "nombre" }
            required: true
        },
        value: {
            type: [Number, String],
            required: false
        }
    },

    data() {
        return {
            filter: '',
            showOptions: false,
            selectedValue: this.initial || '',
            filteredOptions: this.options
        };
    },

    computed: {
        selectedText() {
            return this.options[this.selectedValue] || '';
        }
    },

    watch: {
        value(newValue) {
            this.selectedValue = newValue;
            this.filter = this.options[newValue] || ''; // 🔹 Actualiza el input visible
        },
        options: {
            handler() {
                this.filterOptions();
            },
            deep: true
        }
    },

    methods: {
        clearField() {
            this.filter = ''; // 🔹 Borra el input de búsqueda
            this.selectedValue = ''; // 🔹 Resetea el valor seleccionado
            this.showOptions = false; // 🔹 Cierra el dropdown si estaba abierto
            this.filteredOptions = this.options; // 🔹 Restaura las opciones originales
        },
        filterOptions() {
            const filter = (this.filter || '').toLowerCase();
            this.filteredOptions = Object.fromEntries(
                Object.entries(this.options).filter(([key, value]) =>
                    (value ? value.toString().toLowerCase() : '').includes(filter)
                )
            );
        },

        selectOption(key, option) {
            this.selectedValue = key;
            this.filter = option;
            this.showOptions = false;
            this.$emit('input', key);
        },
        onFocus() {
            this.showOptions = true;
        },
        closeDropdown(event) {
            if (this.$refs.dropdown && !this.$refs.dropdown.contains(event.target)) {
                this.showOptions = false;
            }
        }
    },
    mounted() {
        this.$set(this.$parent.fields, this.name, this.initial);
        document.addEventListener('click', this.closeDropdown);
    },

    beforeDestroy() {
        document.removeEventListener('click', this.closeDropdown);
    }
};
</script>

<style>
.filter-select {
    position: relative;
    width: 100%;
}
.select-wrapper {
    position: relative;
    width: 100%;
}
.form-field {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: text; /* 🔹 No parecerá un botón */
}
.dropdown-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    font-size: 12px;
    color: #666;
}
.options-list {
    position: absolute;
    width: 100%;
    border: 1px solid #ccc;
    background: white;
    z-index: 1000;
    max-height: 150px;
    overflow-y: auto;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    padding: 0;
    margin: 0;
    list-style: none;
    border-radius: 4px;
}
.option-item {
    padding: 10px;
    cursor: pointer;
}
.option-item:hover {
    background-color: #f0f0f0;
}
</style>
