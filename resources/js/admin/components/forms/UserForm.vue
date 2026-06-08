<script>
    import BaseForm from '../../../main/components/forms/base/BaseForm.vue';

    export default {
        extends: BaseForm,


        data() {
            return {
                fields: {
                    roles: [],
                    is_active:0
                }
            };
        }
    };
</script>