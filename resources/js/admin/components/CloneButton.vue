<template>
    <button :class="classes" type="button" @click="showConfirmation">
        <slot>Clonar</slot>
    </button>
</template>

<script>
export default {
    props: {
        item: { type: Object, required: true },
        url: { type: String, required: true }, // Asegurar que la URL se pase como prop
        classes: { type: String, default: 'btn btn--sm btn-nowrap btn--success' }
    },
    methods: {
        showConfirmation() {
            window.swal({
                title: "¿Quieres clonar esta venta?",
                text: "Se creará una nueva venta con los mismos datos.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#5a8",
                confirmButtonText: "Clonar",
                showLoaderOnConfirm: true,
                preConfirm: () => this.cloneItem()
            });
        },

        cloneItem() {
            if (!this.url) {
                console.error("Error: URL no definida.");
                window.swal("Error", "No se pudo clonar el elemento.", "error");
                return;
            }

            return window.axios.post(this.url, { item: this.item }, { 
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                }
            }).then(response => {
                if (response.data.success && response.data.newItem) {
                    this.$emit('clone', response.data.newItem);
                    window.swal("Clonación exitosa", "Se ha creado una nueva venta.", "success");
                } else {
                    throw new Error("No se pudo clonar el elemento.");
                }
            }).catch(error => {
                window.swal("Error", "No se pudo clonar el elemento.", "error");
                console.error("Error al clonar:", error);
            });
        },
    }
};
</script>

