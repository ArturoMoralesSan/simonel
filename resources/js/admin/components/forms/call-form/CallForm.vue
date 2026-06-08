<template>
    <form>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Datos de convocatoria
            </h3>

            <div class="md:row">
                <div class="md:col-2/3">
                    <div class="form-control">
                        <label for="title">Titulo</label>
                        <text-field name="title" v-model="fields.title" :initial="call.title || ''">
                        </text-field>
                        <field-errors name="title"></field-errors>
                    </div>
                    <div class="form-control">
                        <label for="category">Categoría</label>
                        <select-field name="category"  v-model="fields.category" :options="categories" :initial="call.category_id || null ">
                        </select-field>
                        <field-errors name="category"></field-errors>
                    </div>
                    <div class="form-control">
                        <label for="type">Tipo de convocatoria</label>
                        <select-field name="type"
                            @input="changedFiles"
                            v-model="fields.type"
                            :options="call_types"
                            :initial="call.call_type_id || null"
                        >
                        </select-field>
                        <field-errors name="type"></field-errors>
                    </div>
                    <div class="form-control">
                        <label for="addressed">Dirigido a </label>
                        <select-field name="addressed"  v-model="fields.addressed" :options="addressed" :initial="call.addressed_id || null ">
                        </select-field>
                        <field-errors name="addressed"></field-errors>
                    </div>
                    <div class="form-control">
                        <label for="description">Descripción</label>
                        <text-field name="description" v-model="fields.description" :initial="call.description || ''">
                        </text-field>
                        <field-errors name="description"></field-errors>
                    </div>
                    <div v-if="Object.keys(call).length != 0" class="md:row">
                        <div class="md:col-3/4">
                            <div class="form-control">
                                <label for="document">Documento</label>
                                <file-field name="document" v-model="fields.document" aria-describedby="document-specs"></file-field>
                                <field-errors name="document"></field-errors>
                                <ul id="document-specs" class="description color-gray-darken-1">
                                    <li>Tamaño máximo: 5 MB.</li>
                                    <li>Sólo archivos con extensión pdf.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="md:col-1/4">
                            Documento adjunto <br>
                            <a :href="$root.path+'/archivos/convocatorias/'+call.slug+'/'+call.document"> {{ call.title }} </a>

                        </div>
                    </div>
                    <div v-else>
                        <div class="form-control">
                            <label for="document">Documento</label>
                            <file-field name="document" v-model="fields.document" aria-describedby="document-specs"></file-field>
                            <field-errors name="document"></field-errors>
                            <ul id="document-specs" class="description color-gray-darken-1">
                                <li>Tamaño máximo: 5 MB.</li>
                                <li>Sólo archivos con extensión pdf.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="md:row mb-12">
                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label for="liberation-date">Fecha de inicio</label>

                                <date-field name="start_date"
                                    v-model="fields.start_date"
                                    :initial="call.start_date || ''"
                                    :aria-describedby="supportsDates ? '' : 'start-date-description'"
                                ></date-field>

                                <field-errors name="start_date"></field-errors>

                                <p v-if="! supportsDates" id="start-date-description" class="description">
                                    Formato: dd/mm/aaaa
                                </p>
                            </div>
                        </div>
                        <div class="md:col-1/2">
                            <div class="form-control">
                                <label for="liberation-date">Fecha de termino</label>

                                <date-field name="finish_date"
                                    v-model="fields.finish_date"
                                    :initial="call.finish_date || ''"
                                    :aria-describedby="supportsDates ? '' : 'finish-date-description'"
                                ></date-field>

                                <field-errors name="finish_date"></field-errors>

                                <p v-if="! supportsDates" id="finish-date-description" class="description">
                                    Formato: dd/mm/aaaa
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Portada
            </h3>
            <div class="md:row">
                <div class="md:col-2/3">
                    <div class="form-control">
                        <label for="cover">Documento</label>
                        <file-field name="cover" v-model="fields.cover" aria-describedby="cover-specs"></file-field>
                        <field-errors name="cover"></field-errors>
                        <ul id="cover-specs" class="description color-gray-darken-1">
                            <li>Tamaño máximo: 5 MB.</li>
                            <li>Sólo archivos con extensión jpg, .png.</li>
                        </ul>
                    </div>
                </div>
                <div class="md:col-1/3">
                    <div v-if="call.cover">
                        <figcaption class="preview-box__caption">
                            Portada actual
                        </figcaption>
                        <figure class="preview-aside__box preview-box">
                            <img :src="$root.path+'/archivos/convocatorias/'+call.slug+'/'+call.cover" alt="">
                        </figure>
                    </div>
                    <div v-else>
                        <figcaption class="preview-box__caption">
                            Sin portada
                        </figcaption>
                    </div>

                </div>
            </div>
        </section>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Enlaces de convocatoria
            </h3>
            <div class="md:row">
                <div class="md:col-2/3">
                    <CallFileForm v-for="i in fields.files_count" :key="i"
                        :index="i"
                        :call="call"
                        :call_file="call.call_files || []"
                        :errors="errors"
                        :fields="fields"
                        @remove="removeFile"
                    >
                    </CallFileForm>

                    <p class="pt-4">
                        <button v-if="fields.files_count < files"
                            class="btn btn--light mr-4"
                            type="button"
                            @click="fields.files_count++"
                        >
                            <img class="mr-1 align-top"
                                :src="$root.path + '/img/icons/plus-circle-primary.svg'"
                                alt=""
                                width="20px"
                            >
                            <span class="align-top">Agregar archivos</span>
                        </button>
                        Puedes agregar un máximo de {{ files }} archivos.
                    </p>
                </div>
            </div>
        </section>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Periodos de convocatoria
            </h3>
            <div class="md:row">
                <div class="md:col-2/3">
                    <CallPeriodForm v-for="i in fields.period_count" :key="i"
                        :index="i"
                        :file_types="files_types"
                        :call_period="call.call_periods || []"
                        :errors="errors"
                        :fields="fields"
                        @remove="removePeriod"
                    >
                    </CallPeriodForm>

                    <p class="pt-4">
                        <button v-if="fields.period_count < period"
                            class="btn btn--light mr-4"
                            type="button"
                            @click="fields.period_count++"
                        >
                            <img class="mr-1 align-top"
                                :src="$root.path + '/img/icons/plus-circle-primary.svg'"
                                alt=""
                                width="20px"
                            >
                            <span class="align-top">Agregar periodo</span>
                        </button>
                        Puedes registrar a un máximo de {{ period }} periodos.
                    </p>
                </div>
            </div>
        </section>
        <div class="text-center pt-8">
            <form-button class="btn--primary btn--wide">
                Enviar
            </form-button>
        </div>
   </form>
</template>

<script>
    import BaseForm from '../../../../main/components/forms/base/BaseForm';
    import CallPeriodForm from './CallPeriodForm.vue';
    import CallFileForm from './CallFileForm.vue';
    import ProfileGalleryCard from '../../../../main/components/ProfileGalleryCard.vue';

    export default {
        extends: BaseForm,

        components: { CallPeriodForm, CallFileForm, ProfileGalleryCard},
        props: {
            period: {
                required: true,
                type: Number
            },
            files: {
                required: true,
                type: Number
            },
            filesurl: {
                required: true,
                type: String
            },
            call_types: {
                required: true,
                type: Object
            },
            categories: {
                required: true,
                type: Object
            },
            addressed: {
                required: true,
                type: Object
            },
            file_types: {
                required: true,
                type: Object
            },
            call: {
                required:false,
                type: Object
            },
            period_count: {
                required: false,
                type: Number
            },
            files_count: {
                required: false,
                type: Number
            },
            period_array: {
                required: false,
                type: Array
            }
        },
        data() {
            return {
                firstTime: null,
                fields: {
                    period_count: this.period_count,
                    files_count: this.files_count,
                },
                files_types: this.file_types,
            };
        },

        watch: {

            firstTime: function(val) {
                this.fields._method = val === false ? 'patch' : 'post';
            }
        },
        mounted() {
            if (this.fields.type) {
                this.changedFiles(this.fields.type);
            }

        },

        methods: {
            /**
             * Copy all Period's fields from one card to another.
             *
             * @param {Integer} source
             * @param {Integer} target
             */
            copyPeriodFields(source, target) {
                const regex = new RegExp('^period' + source + '_');

                this.deletePeriodFields(target);

                for (let field in this.fields) {
                    if (regex.test(field)) {
                        this.$set(this.fields, field.replace(source, target), this.fields[field]);
                    }
                }
            },

            copyFileFields(source, target) {
                const regex = new RegExp('^file' + source + '_');

                this.deletePeriodFields(target);

                for (let field in this.fields) {
                    if (regex.test(field)) {
                        this.$set(this.fields, field.replace(source, target), this.fields[field]);
                    }
                }
            },


            /**
             * Delete all fields for the given Period.
             *
             * @param {Integer} index
             */
            deletePeriodFields(index) {
                const regex = new RegExp('^period' + index + '_');

                for (let field in this.fields) {
                    if (regex.test(field)) {
                        delete this.fields[field];
                    }
                }
            },


            deleteFileFields(index) {
                const regex = new RegExp('^file' + index + '_');

                for (let field in this.fields) {
                    if (regex.test(field)) {
                        delete this.fields[field];
                    }
                }
            },


            /**
             * Copy all necessary Period fields to move their index
             * and then remove the last card.
             *
             * @param {Integer} index
             */
            removePeriod(index) {
                for (let i = 0; i < this.fields.period_count - index; i ++) {
                    this.copyPeriodFields(index + i + 1, index + i);
                }

                this.fields.period_count--;

                this.deletePeriodFields(this.fields.period_count + 1);
            },

            removeFile(index) {
                for (let i = 0; i < this.fields.files_count - index; i ++) {
                    this.copyFileFields(index + i + 1, index + i);
                }

                this.fields.files_count--;

                this.deleteFileFields(this.fields.files_count + 1);
            },

            changedFiles: function(value) {
                axios.post(this.filesurl+'/'+value)
                .then(response => {
                    this.files_types = response.data.types_files;
                });
            },
        }
    };
</script>
