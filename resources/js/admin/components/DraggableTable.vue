<template>
    <div>
        <p v-if="! resourceList.length" class="mb-12 text-center">
            No hay archivos publicados.
        </p>

        <template v-else>
            <p v-if="resourceList.length > 1" class="mb-12">
                Arrastra los archivos para cambiar el orden en el que se muestran.
            </p>

            <slot
                :wideView="wideView"
                :resourceList="resourceList"
                :onResourceDelete="onResourceDelete"
                :forceFallback="forceFallback"
            ></slot>
        </template>
    </div>
</template>

<script>
    import ResourceTable from './ResourceTable.vue';

    export default {
        extends: ResourceTable,

        props: {
            resourceField: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                forceFallback: navigator.userAgent.toLowerCase().indexOf('firefox') > -1
            };
        },

        watch: {
            resourceList: function() {
                this.emitOrder();
            }
        },

        mounted() {
            this.emitOrder();
        },

        methods: {
            /**
             * Send order data to parent component.
             */
            emitOrder() {
                this.$parent.fields[this.resourceField] = this.resourceList.map((item, i) => {
                    return {
                        order: i + 1,
                        id: item.id
                    };
                });
            }
        }
    };
</script>
