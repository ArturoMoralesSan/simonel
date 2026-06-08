<template>
    <div>
        <div v-show="! expanded"
            v-html="truncatedText"
        >
        </div>

        <p v-show="! expanded">
            <button class="btn-link"
                @click="expanded = true"
            >
                <span class="align-bottom">Mostrar más</span>
                <img :src="$root.path + '/img/icons/plus-circle-link.svg'"
                    alt=""
                    width="16px"
                >
            </button>
        </p>

        <div v-show="expanded"
            v-html="text"
        >
        </div>

        <p v-show="expanded">
            <button class="btn-link"
                @click="expanded = false"
            >
                <span class="align-bottom">Mostrar menos</span>
                <img :src="$root.path + '/img/icons/minus-circle-link.svg'"
                    width="16px"
                    alt=""
                >
            </button>
        </p>
    </div>
</template>

<script>
    export default {
        props: {
            text: {
                type: String,
                required: true
            },

            truncatedText: {
                type: String,
                required: true
            }
        },

        data() {
            return {
                expanded: false
            };
        }
    };
</script>
