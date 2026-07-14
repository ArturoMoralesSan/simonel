<template>
    <select
        class="form-field"
        :name="name"
        @change="selectedChange($event)"
    >
        <option
            v-for="(option, key) in options"
            :key="key"
            :selected="selected == key"
            :value="key"
        >
            {{ option }}
        </option>
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
            default: '',
            required: false
        },
        options: {
            type: Object,
            required: true
        },
        url: {
            type: String,
            required: true
        }
    },

    methods: {

        selectedChange(e) {

            axios.post(this.url, {
                role_id: e.target.value
            })
            .then(response => {

                if (response.headers['redirect-to']) {
                    window.location.href = response.headers['redirect-to'];
                }

            });

        }

    }
}
</script>