<template>
    <div class="box box--lg bg-white b-1 rounded relative">
        <h3>
            Periodo entrega {{ index }}
        </h3>

        <button v-if="index > 0 &&  deliveries_count <= 0"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('remove', index)"
        >
            Eliminar
        </button>
        <div class="row mb-4">
            <div class="col">
                <text-field :name="'period' + index + '_id'"
                        v-model="fields['period' + index + '_id']"
                        :type="'hidden'"
                        maxlength="10"
                        :initial="((typeof call_period[index-1] !== 'undefined') ? call_period[index-1].id.toString() : '')"
                    >
                    </text-field>
                <div class="form-control">
                    <label :for="'period' + index + '-description'">Descripción</label>
                    <text-field :name="'period' + index + '_description'"
                        v-model="fields['period' + index + '_description']"
                        maxlength="60"
                        :initial="((typeof call_period[index-1] !== 'undefined') ? call_period[index-1].description : '')"
                    >
                    </text-field>

                    <field-errors :name="'period' + index + '_description'"></field-errors>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col">
                <div v-for="i in fields['period' + index + '_files_count']" :key="i" class="form-control">
                    <label for="file_type">Tipo de archivo</label>
                    <button v-if="i > 1"
                        class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
                        type="button"
                        @click="removeFile(index, i)"
                    >
                        Eliminar
                    </button>
                    <select-field :name="'period' + index +'_'+ i + '_file_type'"
                        v-model="fields['period' + index +'_'+ i + '_file_type']"
                        :options="file_types"
                        :initial="((typeof call_period[index-1] !== 'undefined') ? call_period[index-1].id : null)"
                    >
                    </select-field>

                    <field-errors :name="'period' + index +'_'+ i + '_file_type'"></field-errors>
                </div>

                <button v-if="fields['period' + index + '_files_count'] < 5"
                    class="btn btn--light btn--sm"
                    type="button"
                    @click="addEl(index)"
                >
                    <span class="align-top">Agregar tipo de archivo</span>
                </button>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-1/2">
                <div class="form-control">
                    <label for="start-date">Fecha de inicio</label>
                    <date-field :name="'period' + index + '_start_date'"
                        v-model="fields['period' + index + '_start_date']"
                        :initial="((typeof call_period[index-1] !== 'undefined') ? call_period[index-1].start_date : '')"
                        :aria-describedby="supportsDates ? '' : 'start-date-description'"
                    ></date-field>

                    <field-errors :name="'period' + index + '_start_date'"></field-errors>

                    <p v-if="! supportsDates" :id="'period' + index + '-start_date'-description" class="description">
                        Formato: dd/mm/aaaa
                    </p>
                </div>
            </div>
            <div class="col-1/2">
                <div class="form-control">
                    <label for="finish-date">Fecha de termino</label>
                    <date-field :name="'period' + index + '_finish_date'"
                        v-model="fields['period' + index + '_finish_date']"
                        :initial="((typeof call_period[index-1] !== 'undefined') ? call_period[index-1].finish_date : null)"
                        :aria-describedby="supportsDates ? '' : 'finish-date-description'"
                    ></date-field>

                    <field-errors :name="'period' + index + '_finish_date'"></field-errors>

                    <p v-if="! supportsDates" :id="'period' + index + '-finish_date'-description" class="description">
                        Formato: dd/mm/aaaa
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import TextField from '../../../../main/components/forms/base/TextField.vue';
    import FieldErrors from '../../../../main/components/forms/base/FieldErrors.vue';
    import DateField from '../../../../main/components/forms/base/DateField.vue';
    import SelectField from '../../../../main/components/forms/base/SelectField.vue';

    export default {
        components: { DateField, FieldErrors, TextField, SelectField },

        props: {
            index: {
                type: Number,
                required: true
            },
            file_types: {
                type: Object,
                required: true
            },
            call_period: {
                type: Array,
                required: false
            },

            errors: {
                type: Object,
                required: true
            },
            fields: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                supportsDates: null,
                deliveries_count: 0,
            };
        },

        created() {
            if (this.call_period[this.index-1] !== undefined) {
                this.deliveries_count = this.call_period[this.index-1].project_deliveries.length;
            }
            var count = 1;
            if (this.call_period.length != 0) {
                if (this.call_period[this.index-1] !== undefined) {
                    count = this.call_period[this.index-1].type_project_files.length;
                }
            }

            this.$set(this.fields, 'period' + this.index + '_files_count', count);


        },
        mounted() {
            if (this.call_period.length != 0) {
                if (this.call_period[this.index-1] !== undefined) {
                    for (var i = 1; i <= this.fields['period' + this.index + '_files_count']; i++) {
                        this.fields['period' + this.index +'_'+ i +'_file_type'] = this.call_period[this.index-1].type_project_files[i-1].id;
                    }
                }
            }

        },

        methods: {

            addEl(index) {
                var count = this.fields['period' + index + '_files_count']+ 1;
                this.$set(this.fields, 'period' + index + '_files_count', count);


            },

            /**
             * Copy all necessary Period fields to move their index
             * and then remove the last card.
             *
             * @param {Integer} index
             */
            removeFile(index, i) {

                this.fields['period' + index +'_'+ i +'_file_type'] = this.fields['period' + index +'_'+ (i+1) +'_file_type'];
                this.fields['period' + this.index + '_files_count']--;
            }

        }
    };
</script>
