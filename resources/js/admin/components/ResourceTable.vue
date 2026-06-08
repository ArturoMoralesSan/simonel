<script>
    export default {
        props: {
            breakpoint: {
                default: 800
            },
            model: {
                default: () => []
            },
        },

        data() {
            return {
                resourceList: this.model,
                wideView: true
            };
        },

        watch: {
            resourceList: function() {
                this.updateRootCount();
            }
        },

        mounted() {
            this.updateRootCount();

            const mq = window.matchMedia(`screen and (min-width:${this.breakpoint}px)`);

            mq.addListener(this.match);

            this.wideView = mq.matches;

            this.$root.$on(this.uploadEvent, this.addItem);
        },

        methods: {

           
            cloneResource(newSale) {
                this.resourceList.unshift(newSale); 
                this.updateRootCount();
            },

            /**
             * Add class to display table for large viewports.
             *
             * @param {Object} e
             */
            match(e) {
                this.wideView = e.matches;
            },


            /**
             * Remove row after the resource has been deleted.
             *
             * @param {DeleteButton} DeleteButton
             */
            onResourceDelete(DeleteButton) {
                const index = this.resourceList.map(item => item.id).indexOf(DeleteButton.resourceId);

                this.resourceList.splice(index, 1);

                DeleteButton.showSuccess();
            },


            /**
             * Update the count of resource items in root component.
             */
            updateRootCount() {
                this.$root.resourceCount = this.resourceList.length;
            },
            
        }
    };
</script>
