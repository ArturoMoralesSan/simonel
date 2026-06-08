<template>
	<select class="form-field category__select-orderby" :name="name" @change="selectedChange($event)">
        <option selected value="">Selecciona una opción</option>
		<option v-for="(option, key) in options" :selected="selected == key" :value="key">{{ option }}</option>
	</select>
    
</template>
<script>
    export default {
        props: {
            name: {
                type: String,
                required: true
            },
            selected: {
                type: String,
            	default:false,
            	required:false
            },
            options: {
                type: Object,
                required: true
            },
        },

		methods: {

			selectedChange(e) {
				let queryString = window.location.search;
				let urlParams = new URLSearchParams(queryString);
				let reloadUrl = this.$root.path + window.location.pathname;
				urlParams.set(this.name, e.target.value)
				urlParams.delete('page');
				let urlParameter = reloadUrl+'?'+urlParams.toString();
				window.location.href = urlParameter;

			}
        }
    };
</script>