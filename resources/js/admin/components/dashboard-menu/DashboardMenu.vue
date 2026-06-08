<template>
    <transition
        :enter-active-class="isLoading ? '' : 'dashboard-menu--enter'"
        :leave-active-class="isLoading ? '' : 'dashboard-menu--leave'"
    >
        <nav id="dashboard-menu"
            class="dashboard-menu"
            :class="{ 'dashboard-menu--visible' : menuIsVisible }"
            v-show="menuIsVisible"
        >
            <div class="dashboard-menu__overlay" @click="$emit('toggle-menu')"></div>

            <ul class="dashboard-menu__col-sections">
                <li class="dashboard-menu__section"
                    :class="{'dashboard-menu__section--active' : activeSection === section.slug }"
                    v-for="(section, i) in menu"
                    :key="i"
                    @click="activeSection = section.slug"
                >
                    <img class="dashboard-menu__tile" :src="$root.path + '/archivos/dashboard/tiles/' + section.tile" alt="">
                    {{ section.name }}
                </li>
            </ul>

            <div class="dashboard-menu__col-submenus">

                <dashboard-column v-for="section in menu" :key="section.id"
                    :section="section"
                    v-show="activeSection === section.slug"
                ></dashboard-column>

            </div>
        </nav>
    </transition>
</template>

<script>
    import DashboardColumn from './DashboardColumn.vue';

    export default {
        components: { DashboardColumn },

        props: {
            isLoading     : Boolean,
            menuIsVisible : Boolean,
            menu          : undefined
        },

        data() {
            return {
                activeSection: ''
            };
        },

        created() {
            this.setActiveSection();
        },


        methods: {
            /**
             * Set active section when module loads.
             */
            setActiveSection() {
                if (this.menu.isArray) {
                    return this.activeSection = this.menu.filter(section => section.active)[0].slug;
                }

                for (let key in this.menu) {
                    if (this.menu[key].active) {
                        this.activeSection = this.menu[key].slug;
                    }
                }
            }
        }
    };
</script>
