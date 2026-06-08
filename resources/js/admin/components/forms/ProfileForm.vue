<script>
    import BaseForm from '../../../main/components/forms/base/BaseForm.vue';

    export default {
        extends: BaseForm,

        props: {
            avatar: {
                required: true
            }
        },

        data() {
            return {
                hasAvatar: !! this.avatar
            };
        },

        methods: {
            /**
             * Provide feedback when user deletes an avatar.
             */
            onAvatarDelete(DeleteButton) {
                this.hasAvatar = false;

                DeleteButton.showSuccess();
            }
        }
    };
</script>
