<script>
    export default {
        props: {
            dashboardMenuIsVisible: {
                type     : Boolean,
                required : false,
                default  : false
            }
        },

        data() {
            return {
                profileMenuVisible: false
            };
        },

        computed: {
            atDashboard: function() {
                return document.body.classList.contains('dashboard');
            }
        }
    };
</script>
