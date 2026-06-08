<template>
    <form>
        <section class="db-panel">
            <h3 class="db-panel__title">
                Reseña
            </h3>

            <div class="md:row">
                <div class="md:col-2/3">
                    <div class="form-control">
                        <label for="matricula">Matrícula</label>
                        <text-field class="field-get-researcher"  name="matricula" v-model="fields.matricula" maxlength="160" :initial="review.matricula  || '' "></text-field>
                        <button class="btn btn--md rounded-0"
                            type="button"
                            @click="GetData"
                        >
                        Buscar
                    </button>
                        <span :ref="'nombre_matricula'" class="field-researcher__name"></span>
                        <div class="alert alert--warning alert--tiny" style="display:none;" :ref="'error_matricula'"></div>
                        <field-errors name="matricula"></field-errors>
                    </div>

                    <div class="form-control">
                        <span></span>
                    </div>

                    <div class="form-control">
                        <label for="area">Área de conocimiento</label>
                        <select-field name="area"  v-model="fields.area" :options="areasconocimiento" :initial="review.area_conocimiento  || '' ">
                        </select-field>
                        <field-errors name="area"></field-errors>
                    </div>

                    <div class="form-control">
                        <label for="review">Reseña</label>
                        <text-area name="review" rows="10" cols="50" v-model="fields.review" maxlength="2000">{{ review.review  || '' }} </text-area>

                        <field-errors name="review"></field-errors>
                    </div>
                </div>
            </div>
        </section>
        <section class="db-panel mb-16">
            <h3 class="db-panel__title">
                Foto
            </h3>
            <div class="md:row">
                <div class="preview-aside">
                    <figure class="preview-aside__box preview-box">
                        <img style="width: 100px;" :src="$root.path+'/archivos/investigadores/'+review.image" alt="">
                        <figcaption class="preview-box__caption">
                            Imagen actual
                        </figcaption>
                    </figure>
                </div>

                <div class="md:col-2/3">
                    <div class="form-control">
                        <label for="image">Agregar imagen</label>

                        <file-field name="image" v-model="fields.image"></file-field>

                        <field-errors name="image"></field-errors>
                        <ul id="image-specs" class="description color-gray-darken-1">
                            <li>
                                Tamaño máximo: 1 MB.
                            </li>
                            <li>
                                Sólo archivos con extensión jpeg, gif, png.
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        </section>

        <div class="text-center">
            <form-button class="btn--dashboard btn--wide">
                Actualizar
            </form-button>
        </div>
    </form>
</template>

<script>
    import BaseForm from '../../../main/components/forms/base/BaseForm';

    export default {
        extends: BaseForm,
        props: {
            areasconocimiento: {
                required:true,
                type: Object
            },
            review: {
                required:false,
                type: Object
            }, 
            researcherurl : {
                required:true,
                type: String
            }
        },

         methods: {
            GetData: function (e) {
                e.preventDefault();
                var campoerror = this.$refs['error_matricula'];
                var camponombre = this.$refs['nombre_matricula'];

                campoerror.innerText = '';
                campoerror.style.display = 'none';

                camponombre.innerText = '';
                camponombre.style.display = 'none';

                window.axios.post(this.researcherurl, {
                    'matricula' : this.fields['matricula']
                }).then(response => {
                            console.log(response.status== 200)
                    if (response.data.exist) {
                        camponombre.innerText = response.data.value;
                        camponombre.style.display = 'block';
                    } else {

                        campoerror.innerText = 'No se encontraron datos de la matrícula '+this.fields['matricula']+'.';
                        campoerror.style.display = 'block';
                    }

                });
            }
        },
    }    
</script>