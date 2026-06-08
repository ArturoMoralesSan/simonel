<template>
    <div class="presentation">
         <div class="lg:row">
            <div class="presentation__image-col">
                <span class="presentation__image"></span>
            </div>

            <div class="lg:col-2/3 presentation__text">
                <p>
                    La Dirección Institucional de Posgrado e Investigación de la UJED tiene como objetivo principal
                    apoyar a la formación de recursos humanos capacitados en la investigación para fortalecer las
                    actividades de investigación científica, el conocimiento y la innovación, favoreciendo la
                    colaboración entre diferentes grupos académicos, lo cual permitirá aportar elementos
                    fundamentales para incrementar la cultura en el desarrollo de la investigación, aumentando los
                    niveles de productividad y competitividad, consolidándola como una herramienta para resolver
                    problemas locales, regionales y nacionales, promoviendo su desarrollo económico a través de las
                    aportaciones en materia de ciencia, tecnología, innovación y calidad. Esto con la finalidad de que
                    los profesores y alumnos de nuestra Universidad se inicien en la investigación académica y
                    trabajen en conjunto tomando en cuenta los intereses de ambos y con ello obtener productos
                    evaluables y metodológicamente sólidos; ésta es una estrategia importante para la formación de
                    recursos humanos orientados bajo un modelo educativo innovador centrado en el aprendizaje y
                    en la investigación científica de alto nivel, consolidando a la UJED como una Institución de
                    vanguardia.
                </p>
                <p>
                    Este sitio tiene como objetivo brindar un espacio donde tanto los docentes como los alumnos
                    puedan desarrollar sus actividades referentes a proyectos de investigación, en donde puedan
                    intercomunicarse mediante el uso de herramientas que soporten y faciliten los procesos de
                    enseñanza-aprendizaje a través el uso de las tecnologías de la información.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            breakpoint: {
                type: Number,
                required: true
            }
        },

        data() {
            return {
                breakpointMatches: false,
            };
        },

        mounted() {
            this.setMatchMedia();
        },

        methods: {

            /**
             * Initialize media query.
             */
            setMatchMedia() {
                const mq = window.matchMedia(`screen and (min-width:${this.breakpoint}px)`);

                mq.addListener(this.reset);

                this.reset(mq);
            },


            /*
            |------------------------------------------------------------------------
            | Visibility
            |------------------------------------------------------------------------
            */

            /**
             * Immediately show or hide menu list when media match is triggered.
             *
             * @param {Object} e
             */
            reset(e) {
                this.breakpointMatches = e.matches;
            }
        }
    };
</script>