<template>
    <div class="box box--lg bg-white b-1 rounded relative">
        <h3>
            Enlace {{ index }}
        </h3>

        <button v-if="index !== 0"
            class="btn btn--danger btn--xs rounded-0 absolute top-0 right-0"
            type="button"
            @click="$emit('remove', index)"
        >
            Eliminar
        </button>
        <div class="row mb-4">
            <div class="col">
                <text-field :name="'file' + index + '_id'"
                        v-model="fields['file' + index + '_id']"
                        :type="'hidden'"
                        maxlength="10"
                        :initial="((typeof call_file[index-1] !== 'undefined') ? call_file[index-1].id.toString() : '')"
                    >
                    </text-field>
                <div class="form-control">
                    <label :for="'file' + index + '-title'">Titulo</label>
                    <text-field :name="'file' + index + '_title'"
                        v-model="fields['file' + index + '_title']"
                        maxlength="60"
                        :initial="((typeof call_file[index-1] !== 'undefined') ? call_file[index-1].title : '')"
                    >
                    </text-field>
                    <field-errors :name="'file' + index + '_title'"></field-errors>
                </div>
            </div>
        </div>
        <div v-if="(typeof call_file[index-1] !== 'undefined')" class="row mb-4">
            <div class="md:col-3/4">
                <div class="form-control">
                    <label for="document">Documento</label>
                    <file-field :name="'file' + index + '_document'"
                        v-model="fields['file' + index + '_document']"
                        :aria-describedby="'file' + index + '_document-specs'">
                    </file-field>
                    <field-errors :name="'file' + index + '_document'"></field-errors>
                    <ul :id="'file' + index + '_document-specs'" class="description color-gray-darken-1">
                        <li>Tamaño máximo: 5 MB.</li>
                        <li>Sólo archivos con extensión pdf.</li>
                    </ul>
                </div>
            </div>
            <div class="md:col-1/4">
                Documento adjunto <br>
                <a :href="$root.path+'/archivos/convocatorias/'+call.slug+'/'+call_file[index-1].document"> {{ call_file[index-1].title }} </a>

            </div>
        </div>
        <div v-else class="row mb-4">
            <div class="col">
                <div class="form-control">
                    <label for="document">Documento</label>
                    <file-field :name="'file' + index + '_document'"
                        v-model="fields['file' + index + '_document']"
                        :aria-describedby="'file' + index + '_document-specs'">
                    </file-field>
                    <field-errors :name="'file' + index + '_document'"></field-errors>
                    <ul :id="'file' + index + '_document-specs'" class="description color-gray-darken-1">
                        <li>Tamaño máximo: 5 MB.</li>
                        <li>Sólo archivos con extensión pdf.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import TextField from '../../../../main/components/forms/base/TextField.vue';
    import FieldErrors from '../../../../main/components/forms/base/FieldErrors.vue';
    import FileField from '../../../../main/components/forms/base/FileField.vue';
    import SelectField from '../../../../main/components/forms/base/SelectField.vue';

    export default {
        components: { FileField, FieldErrors, TextField, SelectField },

        props: {
            index: {
                type: Number,
                required: true
            },

            call_file: {
                type: Array,
                required: false
            },
            call: {
                type: Object,
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
        }
    };
</script>
