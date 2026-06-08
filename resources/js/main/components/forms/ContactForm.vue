<script>
   import BaseForm from './base/BaseForm.vue';

   export default {
       extends: BaseForm,

       methods: {
            /**
             * Handle redirection after receiving a successful response.
             *
             * @param {Object} response
             */
            onSuccess(response) {
                var alertcontainer = document.querySelector('.alert-container');
                var alert = '<p class="alert alert--has-icon alert--success alert--bordered fade-out mb-8">¡Gracias! Hemos recibido tu mensaje.</p>';
                alertcontainer.insertAdjacentHTML('afterbegin', alert);

                this.fields.name    = '';
                this.fields.email   = '';
                this.fields.message = '';

                setTimeout(function() {
                    alertcontainer.innerHTML = '';
                }, 7000);

                this.isSubmitting = false;
            }
        }
   };
</script>