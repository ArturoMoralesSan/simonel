<template>
    <form class="search-form__dashboard search-form-width" action="" method="get">
        <!--@blur="outSelect"  -->
        <input
            type="date"
            class="form-field"
            name="start_date"
            placeholder="Buscar"
            ref="startdate"
            :value="selectedstart"
        >
        <input
            type="date"
            class="form-field search-form_input"
            name="end_date"
            placeholder="Buscar"
            ref="enddate"
            :value="selectedend"
        >
        <button class=" btn btn--primary search-form_b" type="submit" @click.prevent="search">
            <slot name="svg-search"></slot>
        </button>
    </form>
</template>
<script>
    export default {
        props: {

            selectedstart: {
                type: String,
                default:false,
                required:false
            },
            selectedend: {
                type: String,
                default:false,
                required:false
            },
        },
        methods: {
            search(e) {
                let queryString = window.location.search;
                let urlParams = new URLSearchParams(queryString);
                let reloadUrl = this.$root.path + window.location.pathname;
                urlParams.set('start_date', this.$refs.startdate.value)
                urlParams.set('end_date', this.$refs.enddate.value)
                urlParams.delete('page');
                let urlParameter = reloadUrl+'?'+urlParams.toString();
                window.location.href = urlParameter;

            }
        }
    };
</script>